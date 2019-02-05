<?php
namespace framework\sql\core\query;

use framework\sql\models\Entity;

class PagedList {
	public $list;
	public $rowCount;
	public $filtredRowCount;
	
	/**
	 * 
	 * @param Entity[] $list
	 * @param int $rowCount
	 */
	public function __construct(array $list, int $rowCount, $filtredRowCount = null){
		$this->list = $list;
		$this->rowCount = $rowCount;
		$this->filtredRowCount = $filtredRowCount;
	}
}