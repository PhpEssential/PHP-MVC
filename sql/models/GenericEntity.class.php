<?php
namespace framework\sql\models;

use framework\sql\core\metadata\Field;
use framework\sql\core\DbConnection;

/**
 * Entity with id attribute as primary key (int|bigint| and maybe big unsigned int)
 */
class GenericEntity extends Entity {
	const FIELD_ID = "id";
	
	/**
	 * id field
	 *
	 * @var int
	 */
	public $id;

	function __construct(array $data = array()) {
		parent::__construct($data);
	}

	/**
	 *
	 * @return Field[]
	 */
	public static function discriminantFields() {
		return array (
				static::findField(self::FIELD_ID) 
		);
	}

	/**
	 * Use to set the value of discriminant attributes after insert
	 *
	 * @param $conn DbConnection
	 */
	protected function setDiscriminantValue(DbConnection $conn) {
		$this->id = $conn->getLastId();
	}

	/**
	 * Mapping between database fields and model attributes
	 */
	protected static function fields() {
		return array (
				new Field(self::FIELD_ID, "id", Field::INT) 
		);
	}
}