<?php
namespace framework\sql\utils;

use framework\sql\core\metadata\Field;
use framework\utils\DateUtils;

class SqlUtils {
	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param Field $field
	 * @param mixed $value
	 * @throws \Exception
	 * @return string
	 */
	public static function ensureSqlValue(Field $field, $value) : string {
	
		if($value === null) {
			return "NULL";
		}
		
		switch ($field->dataType) {
			case Field::DATE : return $value->format(DateUtils::SQL_DATE_FORMAT);
			case Field::DATE_TIME : return $value->format(DateUtils::SQL_DATE_TIME_FORMAT);
			case Field::BOOLEAN : return $value ? "1" : "0";
			case Field::FLOAT : return strval($value);
			case Field::INT : return strval($value);
			case Field::TEXT : return htmlspecialchars(str_replace("%", "\\%", str_replace("_", "\\_", str_replace("'", "\\'", $value))));
			default: throw new \Exception("Type inconnu: " . $field->dataType);
		}
	}
	
	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param Field $field
	 * @param mixed $value
	 * @throws \Exception
	 * @return string
	 */
	public static function addQuote(Field $field, string $ensuredValue) : string {
		
		if($ensuredValue === "NULL") {
			return $ensuredValue;
		}
		
		switch ($field->dataType) {
			case Field::DATE : return "'" . $ensuredValue . "'";
			case Field::DATE_TIME : return "'" . $ensuredValue . "'";
			case Field::TEXT : return "'" . $ensuredValue . "'";
			default: return $ensuredValue;
		}
	}
}