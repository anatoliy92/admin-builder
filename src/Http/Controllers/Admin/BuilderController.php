<?php namespace Avl\AdminBuilder\Controllers\Admin;

use Illuminate\Http\Request;
	use Avl\AdminBuilder\Models\{Table, TableData};
	use App\Http\Controllers\Avl\AvlController;
	use App\Models\{ Langs, Sections };
	use Illuminate\Support\Arr;
	use Carbon\Carbon;

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

				$table->section_id = $this->section->id;
				$table->published_at = Carbon::parse($settings['date'] . ' ' .$settings['time'])->format('Y-m-d H:i:s');

				foreach ($this->langs as $lang) {
					$table->{'title_' . $lang->key} = $settings['title_' . $lang->key] ?? $this->section->{'name_' . $lang->key} ?? null;
				}

				if ($table->save()) {
					$names = $request->input('names');

					$tebleHeads = $table->data()->get();
					foreach ($tebleHeads as $head) {
						if (!isset($names[$head->row]) || !array_key_exists($head->col, $names[$head->row])) {
							\DB::table('builder-table-data')->where('table_id', $table->id)->where('row', $head->row)->where('col', $head->col)->delete();
						}
					}

					if (!is_null($request->input('names'))) {

						foreach ($request->input('names') as $rowIndex => $row) {
							foreach ($row as $colIndex => $value) {

								$ifExist = TableData::where([
									'table_id' => $table->id,
									'row' => $rowIndex,
									'col' => $colIndex
								]);

								// Проверяем, заполен ли хоть один элемент массива
								if (true == array_filter($value['translates'], function ($v) { return $v !== null; } )) {

									$translates = [];
									foreach ($this->langs as $lang) {
										$translates['value_' . $lang->key] = $value['translates'][$lang->key];
									}
									$translates = Arr::add($translates, 'head', ($value['head'] === "true") ? true : false);

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

					return ['success' => ['Шаблон <b>'. $table->title_ru .'</b> - сохранен!']];
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
							foreach ($this->langs as $lang) {
								$names[$row][$col]['translates'][$lang->key] = getValue($tableData, $row, $col, $lang->key);
								$names[$row][$col]['head'] = isHead($tableData, $row, $col);
							}
						}
					}
				}

				$settings = [
					'id' => $table->id,
					'good' => $table->good,
					'date' => date('Y-m-d', \strtotime($table->published_at)),
					'time' => date('H:i', \strtotime($table->published_at))
				];
				foreach ($this->langs as $lang) {
					$settings['title_' . $lang->key] = $table->{'title_' . $lang->key} ?? null;
				}

				return [
					'names' => $names,
					'settings' => $settings
				];
			}

			return ['errors' => ['Ошибка при получении данных таблицы']];
		}

		public function getTables ($id)
		{
			$tables = Table::where('section_id', $id)->orderBy('published_at', 'desc')->get();

			return ['tables' => $tables];
		}
}
