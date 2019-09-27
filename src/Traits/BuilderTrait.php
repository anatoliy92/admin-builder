<?php namespace Avl\AdminBuilder\Traits;

use Cache;

trait BuilderTrait
{

	function getBodyTable ($table = null) :array
	{
		if (!is_null($table)) {
			$tableData = $table->data()->get()->toArray();

			if (count($tableData) > 0) {
				for ($row = 0; $row <= getMaxRow($tableData); $row++) {
                    for ($col = 0; $col <= getMaxCol($tableData); $col++) {
                        $return[$row][$col] = [
                            'title' => getValue($tableData, $row, $col),
                            'head' => isHead($tableData, $row, $col)
                        ];
                    }
				}
			}
		}
		return $return ?? [];
	}
}
