<?php namespace Avl\AdminBuilder\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{ Langs, Sections };
use Avl\AdminBuilder\Models\{Table, TableData};
use Illuminate\Http\Request;
use Carbon\Carbon;

class BuilderController extends AvlController
{

		protected $langs = null;

		public function __construct (Request $request) {

			parent::__construct($request);

			$this->langs = Langs::get();
		}

		/**
		 * Страница вывода списка новостей к определенному новостному разделу
		 * @param  int  $id      номер раздела
		 * @param  Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function index($id, Request $request)
		{
			$section = Sections::whereId($id)->firstOrFail();

			$this->authorize('view', $section);

			return view('adminbuilder::builder.index', [
				'sectionId' => $id,
				'existTable' => $section->table ? true : false
			]);
		}

		/**
		 * Метод для обновления определенной записи
		 * @param  Request $request
		 * @param  int  $id      Номер раздела
		 * @return redirect to index method
		 */
		public function update($id, Request $request)
		{
				$section = Sections::findOrFail($id);

				$this->authorize('update', $section);
				// dd($request->input('names'));

				$table = Table::where('section_id', $section->id)->first();
				if (!$table) { $table = new Table(); }

				$table->title = $section->name_ru;
				$table->section_id = $section->id;

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
								if (true == array_filter($value, function ($v) { return $v !== null; } )) {

									$translates = [];
									foreach ($this->langs as $lang) {
										$translates['value_' . $lang->key] = $value[$lang->key];
									}

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

					return ['success' => ['Шаблон <b>'. $table->title .'</b> - сохранен!']];
				}

				return ['errors' => ['Произошла ошибка при сохранении.']];
		}

		/**
		 * Получение данных таблицы
		 * @param  integer $id Номер раздела
		 * @return array or json
		 */
		public function getData ($id = null)
		{
			$table = Table::where('section_id', $id)->first();

			if ($table) {
				$names = [];
				$tableData = $table->data()->get()->toArray();

				if (count($tableData)) {
					for ($row = 0; $row <= getMaxRow($tableData); $row++) {
						for ($col = 0; $col <= getMaxCol($tableData); $col++) {
							foreach ($this->langs as $lang) {
								$names[$row][$col][$lang->key] = getValue($tableData, $row, $col, $lang->key);
							}
						}
					}
				}

				return [
					'names' => $names
				];
			}

			return ['errors' => ['Ошибка при получении данных таблицы']];
		}
}
