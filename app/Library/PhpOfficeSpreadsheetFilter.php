<?php

namespace App\Library;

use \PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Office File Filter Proccess
 */
class PhpOfficeSpreadsheetFilter implements IReadFilter
{
	private $startRow = 0;
	private $endRow   = 0;
	private $columns  = [];

	/**  Get the list of rows and columns to read  */
	public function __construct($startRow, $endRow, $columns) {
		$this->startRow = $startRow;
		$this->endRow   = $endRow;
		$this->columns  = $columns;
	}

	public function readCell($columnAddress, $row, $worksheetName = '') {
		//  Only read the rows and columns that were configured
		if ($row >= $this->startRow && $row <= $this->endRow) {
			if (in_array($columnAddress,$this->columns)) {
				return true;
			}
		}
		return false;
	}
}