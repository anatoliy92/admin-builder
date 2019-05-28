<?php

if (!function_exists('getMaxRow')) {
	function getMaxRow($table = []) : int
	{
		$maxRow = 0;
		foreach ($table as $data) {
			$maxRow = ($data['row'] > $maxRow) ? $data['row'] : $maxRow ;
		}
		return $maxRow;
	}
}

if (!function_exists('getMaxCol')) {
	function getMaxCol($table = []) : int
	{
		$maxCol = 0;
		foreach ($table as $data) {
			$maxCol = ($data['col'] > $maxCol) ? $data['col'] : $maxCol ;
		}
		return $maxCol;
	}
}

if (!function_exists('getValue')) {
	function getValue ($table = [], $row = 0, $col = 0, $locale = null)
	{
		$locale = $locale ?? app()->getLocale();

		$filled = Arr::where($table, function ($value, $key) use ($row, $col) {
			if (($value['row'] == $row) && ($value['col'] == $col)) {
				return true;
			}
			return false;
		});

		return head($filled)['value_' . $locale] ?? null ;
	}

}
