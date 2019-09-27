<?php namespace Avl\AdminBuilder\Controllers\Admin;

use Illuminate\Http\Request;
    use Avl\AdminBuilder\Models\{Table, TableData};
    use App\Http\Controllers\Avl\AvlController;
    use App\Models\{ Langs, Sections };
    use Illuminate\Support\Arr;
    use Carbon\Carbon;
    use Cache;

class BuilderController extends AvlController
{
    protected $langs = null;

    protected $section = null;

    public function __construct (Request $request) {

        parent::__construct($request);

        $this->langs = Langs::get();

        $this->section = Sections::find($request->id) ?? null;
    }

    /**
     * Страница вывода списка новостей к определенному новостному разделу
     * @param  int  $id      номер раздела
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        $this->authorize('view', $this->section);

        return view('adminbuilder::builder.index', [
            'sectionId' => $id,
            'existTable' => ($this->section->tables()->get()->count() > 0) ? true : false
        ]);
    }

    /**
     * Метод для обновления определенной записи
     * @param  Request $request
     * @param  int  $id      Номер раздела
     * @return redirect to index method
     */
    public function update(Request $request)
    {
        $this->authorize('update', $this->section);

        $table = Table::where('section_id', $this->section->id);
        if (!is_null($request->builder)) {
            $table = $table->whereId($request->builder);
        }
        $table = $table->first();

        if (!$table) { $table = new Table(); }

        $settings = $request->input('settings');
        $date = $settings['date'] ?? date('Y-m-d');
        $time = $settings['time'] ?? date('H:i');
        $graph = $request->input('graph');

        $table->section_id = $this->section->id;
        $table->published_at = Carbon::parse($date . ' ' . $time)->format('Y-m-d H:i:s');
        $table->graph = isset($graph['cols']) ? $graph : null;

        foreach ($this->langs as $lang) {
            $table->{'title_' . $lang->key} = $settings['title_' . $lang->key] ?? $this->section->{'name_' . $lang->key} ?? null;
        }

        if ($table->save()) {
            $names = json_decode($request->input('names'));

            $tableHeads = $table->data()->get();
            foreach ($tableHeads as $head) {
                if (!isset($names[$head->row]) || !array_key_exists($head->col, $names[$head->row])) {
                    \DB::table('builder-table-data')->where('table_id', $table->id)->where('row', $head->row)->where('col', $head->col)->delete();
                }
            }

            if (!is_null($settings['descriptions'])) {
                foreach ($this->langs as $lang) {
                    $descriptions['before_' . $lang->key] = $settings['descriptions']['before'][$lang->key];
                    $descriptions['after_' . $lang->key] = $settings['descriptions']['after'][$lang->key];
                }
            }

            if (!is_null($names)) {

                foreach ($names as $rowIndex => $row) {
                    foreach ($row as $colIndex => $value) {

                        $ifExist = TableData::where([
                            'table_id' => $table->id,
                            'row' => $rowIndex,
                            'col' => $colIndex
                        ]);

                        // Проверяем, заполен ли хоть один элемент массива
                        if (true == array_filter((array) $value->translates, function ($v) { return $v !== null; } ) || ($value->hide == 1)) {

                            $translates = [];
                            foreach ($this->langs as $lang) {
                                $translates['value_' . $lang->key] = ($value->hide == 0) ? $value->{'translates'}->{$lang->key} : null;
                            }
                            $translates = Arr::add($translates, 'head', $value->head ? true : false);
                            $translates = Arr::add($translates, 'rowspan', $value->rowspan ?? null);
                            $translates = Arr::add($translates, 'colspan', $value->colspan ?? null);
                            $translates = Arr::add($translates, 'hide', $value->hide ?? 0);

                            if ($ifExist->exists()) {
                                // если такая ячека уже была, то обновляем данные в ячейке
                                $ifExist->update($translates);

                                $ifExist->first()->table->update(['updated_at' => Carbon::now()]);
                            } else {
                                // сохраняем данные ячейки если её не было ранее
                                $ifExist->insert(array_merge([
                                    'table_id' => $table->id,
                                    'row' => $rowIndex,
                                    'col' => $colIndex,
                                    'rowspan' => $value->rowspan ?? null,
                                    'colspan' => $value->colspan ?? null,
                                    'hide' => $value->hide ?? 0,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ], $translates));
                            }

                        } else {
                            // Удаляем запись если ячейка пустая
                            \DB::table('builder-table-data')->where('table_id', $table->id)->where('row', $rowIndex)->where('col', $colIndex)->delete();
                        }

                    }
                }
            }

            $table->update(array_merge(
                $descriptions, [
                    'config' => [
                        'hidenRows' => json_decode($request->input('hidenRows')),
                        'hidenCols' => json_decode($request->input('hidenCols'))
                    ]
                ]
            ));

            $table->refresh();

            foreach ($this->langs as $lang) {
                Cache::forget('table-' . $lang->key . '-' . $table->id);
            }

            return [
                'success' => ['Таблица <b>'. $table->title_ru .'</b> - сохранена!'],
                'table' => $table,
                'heads' => $this->getHeadsTable($table->id),
                'graph' => $table->graph ?? [
                    'type' => 'bar',
                    'cols' => [
                        'x' => [],
                        'y' => []
                    ]
                ]
            ];
        }

        return ['errors' => ['Произошла ошибка при сохранении.']];
    }

    /**
     * Получение данных таблицы
     * @param  integer $id Номер раздела
     * @return array or json
     */
    public function getData (Request $request)
    {
        $table = (is_null($request->table)) ? Table::where('section_id', $this->section->id)->orderBy('published_at', 'desc')->first() : Table::find($request->table);

        if ($table) {
            $names = [];
            $tableData = $table->data()->get()->toArray();

            if (count($tableData)) {
                for ($row = 0; $row <= getMaxRow($tableData); $row++) {
                    for ($col = 0; $col <= getMaxCol($tableData); $col++) {
                        $merge = getMerge($tableData, $row, $col);

                        foreach ($this->langs as $lang) {
                            $names[$row][$col]['translates'][$lang->key] = getValue($tableData, $row, $col, $lang->key);
                            $names[$row][$col]['head'] = $merge['head'] ?? false;
                            $names[$row][$col]['rowspan'] = $merge['rowspan'] ?? null;
                            $names[$row][$col]['colspan'] = $merge['colspan'] ?? null;
                            $names[$row][$col]['hide'] = $merge['hide'] ? 1 : 0;
                        }
                    }
                }
            }

            return [
                'names' => $names,
                'settings' => $this->getSettings($table),
                'heads' => $this->getHeadsTable($table->id),
                'config' => [
                    'hidenRows' => $table->config['hidenRows'] ?? [],
                    'hidenCols' => $table->config['hidenCols'] ?? [],
                ],
                'graph' => $table->graph ?? [
                    'type' => 'bar',
                    'cols' => [
                        'x' => [],
                        'y' => []
                    ]
                ]
            ];
        }

        return ['errors' => ['Ошибка при получении данных таблицы']];
    }

    public function destroy (Request $request)
    {
        $this->authorize('delete', $this->section);

        if (!is_null($request->builder)) {
            $table = Table::find($request->builder);

            if (!is_null($table)) {
                if ($table->data->count() > 0) {
                    $table->data()->delete();
                }

                if ($table->delete()) {
                    return ['success' => ['Таблица удалена']];
                }
            }
        }

        return ['errors' => ['Произошла ошибка при удалении.']];
    }

    public function getTables ($id)
    {
        $tables = Table::where('section_id', $id)->orderBy('published_at', 'desc')->get();

        return ['tables' => $tables];
    }

    public function getHeadsTable ($id)
    {
        $heads = [];
        $table = Table::find($id);

        if (!is_null($table)) {
            $tableData = $table->data()->head()->get()->toArray();

            if (count($tableData)) {
                for ($row = 0; $row <= getMaxRow($tableData); $row++) {
                    for ($col = 0; $col <= getMaxCol($tableData); $col++) {
                        foreach ($this->langs as $lang) {
                            $heads[$row][$col]['translates'][$lang->key] = getValue($tableData, $row, $col, $lang->key);
                        }
                    }
                }
            }

        }
        return $heads ?? [];
    }

    public function getSettings ($table = null):array {
        if (!is_null($table)) {
            $settings = [
                'id' => $table->id,
                'good' => $table->good,
                'date' => date('Y-m-d', \strtotime($table->published_at)),
                'time' => date('H:i', \strtotime($table->published_at))
            ];
            foreach ($this->langs as $lang) {
                $settings['title_' . $lang->key] = $table->{'title_' . $lang->key} ?? null;
                $settings['descriptions']['before'][$lang->key] = $table->{'before_' . $lang->key} ?? null;
                $settings['descriptions']['after'][$lang->key] = $table->{'after_' . $lang->key} ?? null;
            }
        }
        return $settings ?? [];
    }
}
