<?php namespace Avl\AdminBuilder\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
	use Avl\AdminBuilder\Models\{Table, TableData};
	use Illuminate\Http\Request;
	use App\Models\{Menu, Langs};
	use Illuminate\Support\Arr;
	use Carbon\Carbon;
	use Validator;
	use View;

class ConstructorController extends AvlController
{

		protected $langs;

		protected $accessModel = null;

		public function __construct (Request $request) {
			parent::__construct($request);

			$this->accessModel = Menu::where('model', 'Avl\AdminBuilder\Models\Table')->first();

			$this->langs = Langs::all();

			View::share('accessModel', $this->accessModel);
		}

		/**
		 * Страница вывода списка "заготовок таблиц"
		 * @param  Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function index(Request $request)
		{
			$this->authorize('view', $this->accessModel);

			$tables = Table::default()->orderBy('created_at', 'DESC');

			if ($request->ajax()) {
				return $tables->select(['id', 'title_ru as title'])->get()->toArray();
			}

			return view('adminbuilder::constructor.index', [
				'tables' => $tables->paginate(30)
			]);
		}

		/**
		 * Вывод формы на добавление шаблона таблицы
		 * @return view
		 */
		public function create()
		{
				$this->authorize('create', $this->accessModel);

				return view('adminbuilder::constructor.create');
		}

		/**
		 * Метод для добавления новой заготовки таблицы
		 * @param  Request $request
		 * @return redirect to index or create method
		 */
		public function store(Request $request)
		{
			$this->authorize('create', $this->accessModel);

			$validator = Validator::make($request->input(), [
				'tableName' => 'required'
			]);

			if (!$validator->fails()) {
				$table = new Table;

				$table->is_default = true;
				$table->good = true;
				$table->title_ru = $request->input('tableName');

				if ($table->save()) {

					foreach ($request->input('names') as $rowIndex => $row) {
						foreach ($row as $colIndex => $value) {

							if (true == array_filter($value['translates'], function ($v) { return $v !== null; } )) {

								$translates = [];
								foreach ($this->langs as $lang) {
									$translates['value_' . $lang->key] = $value['translates'][$lang->key];
								}
								$translates = Arr::add($translates, 'head', ($value['head'] === "true") ? true : false);

								$filled = new TableData();

								$filled->insert(array_merge([
									'table_id' => $table->id,
									'row' => $rowIndex,
									'col' => $colIndex,
									'created_at' => Carbon::now(),
									'updated_at' => Carbon::now()
								], $translates));
							}
						}
					}
					return [
						'success' => ['Шаблон <b>'. $table->title_ru .'</b> - сохранен!'],
						'redirect' => route('adminbuilder::constructor.index')
					];
				}
			}

			return ['errors' => ['Не указано <b>Название шаблона</b>']];
		}

		/**
		 * Отобразить Шаблон таблицы
		 * @param  int $id      Номер таблицы
		 * @return \Illuminate\Http\Response
		 */
		public function show($id)
		{
			$this->authorize('view', $this->accessModel);

			return view('adminbuilder::constructor.show', [
				'table' => Table::findOrFail($id)
			]);
		}

		/**
		 * Редактирование шаблона таблицы
		 * @param  int $id        Номер шаблона
		 * @return \Illuminate\Http\Response
		 */
		public function edit($id)
		{
			$this->authorize('update', $this->accessModel);

			return view('adminbuilder::constructor.edit', ['id' => $id]);
		}

		/**
		 * Метод для обновления определенной записи
		 * @param  Request $request
		 * @param  int  $id      Номер раздела
		 * @param  int  $news_id Номер записи
		 * @return redirect to index method
		 */
		public function update($id, Request $request)
		{
			$this->authorize('update', $this->accessModel);

			$validator = Validator::make($request->input(), [
				'tableName' => 'required'
			]);

			if (!$validator->fails()) {

				$table = Table::find($id);
				$table->title_ru = $request->input('tableName');

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

					return [
						'success' => ['Шаблон <b>'. $table->title .'</b> - сохранен!'],
						'redirect' => route('adminbuilder::constructor.index')
					];
				}
			}

			return ['errors' => ['Не указано <b>Название шаблона</b>']];
		}

		/**
		 * Удаление шаблона таблицы
		 * @param  int $id      Номер таблицы
		 * @return json
		 */
		public function destroy($id, Request $request)
		{
			$this->authorize('delete', $this->accessModel);

			$table = Table::where('is_default', true)->find($id);

			if ($table) {
				$table->data()->delete();

				if ($table->delete()) {
					return ['success' => ['Шаблон <b>'. $table->title .'</b> - удален!']];
				}
			}

			return ['errors' => ['Ошибка при получении данных шаблона.']];
		}

		/**
		 * Получение данных таблицы
		 * @param  integer $id Номер таблицы
		 * @return array or json
		 */
		public function getData ($id = null)
		{
			$table = Table::find($id);

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

				return [
					'tableName' => $table->title_ru,
					'names' => $names
				];
			}

			return ['errors' => ['Ошибка при получении данных таблицы']];
		}

}
