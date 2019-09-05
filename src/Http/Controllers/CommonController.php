<?php namespace Avl\AdminBuilder\Controllers;

use App\Http\Controllers\Controller;
use Avl\AdminBuilder\Models\{ Table, TableData };
use Illuminate\Http\Request;
use App\Models\Sections;

class CommonController extends Controller
{

	public function getGraphData ($id = null, Request $request)
	{
		if (!is_null($id) && $id > 0) {
			$table = Table::find($id);

			if (!is_null($table)) {

				$cols = $request->input('graph.cols') ?? $table->graph['cols'] ?? null;

				if (!is_null($cols)) {
					if ($cols) {
						if ($cols['x'] && $cols['y']) {

							$index = 0;
							$axisX = $table->data()->whereCol($cols['x'][0])->where('row', '>', $cols['x'][1])->orderBy('row')->orderBy('col')->get();
							foreach ($axisX as $x) {
								$labels[$index] = $x->value_ru;

								$index++;
							}
							$datasets = $this->getDatasets($table, $axisX, $cols['y']);
						}

						return [
							'success' => 'Данные успешно загружены',
							'labels' => $labels ?? [],
							'datasets' => $datasets ?? []
						];
					}
				}
			}
		}

		return ['errors' => 'Данные для построения графика не загружены, либо не были указаны'];
	}

	public function getDatasets ($table = null, $axisX = null, $colsY = [])
	{
		if (!is_null($table)) {
			$datasets = [];

			$index = 0;
			foreach ($colsY as $coordinatesY) {
				$datasetLabel = $table->data()->whereRow($coordinatesY[0])->whereCol($coordinatesY[1])->orderBy('row')->orderBy('col')->first();

				$datasets[$index]['label'] = $datasetLabel->value_ru ?? 'Нет данных';

				$data = [];
				$backgroundColor = [];
				$borderColor = [];
				$borderWidth = [];

				$color = rand (1, 255). ', ' . rand (1, 255). ', ' . rand (1, 255);
				foreach ($axisX as $coordinatesX) {

					$datasetsData = $table->data()->whereRow($coordinatesX->row)->whereCol($datasetLabel->col)->orderBy('row')->orderBy('col')->first();
					$data[] = \preg_replace('/ /', '', $datasetsData->value_ru ?? null);

					$backgroundColor[] = 'rgba(' . $color . ', 0.2)';
					$borderColor[] = 'rgba(' . $color . ', 1)';
					$borderWidth[] = 2;
				}

				$datasets[$index]['data'] = $data ?? [];
				$datasets[$index]['backgroundColor'] = $backgroundColor;
				$datasets[$index]['borderColor'] = $borderColor;
				$datasets[$index]['borderWidth'] = $borderWidth;

				$index++;
			}
			return $datasets ?? [];
		}

		return [];
	}

}
