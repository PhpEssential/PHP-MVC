<?php
namespace framework\ui\form;

use framework\Config;
use framework\exception\IllegalArgumentException;
use framework\internationalization\Message;
use framework\utils\FileUtils;
use framework\utils\LogUtils;

/**
 * Classe permettant de définir un objet comme étant assignable depuis une requète HTTP
 */
abstract class Bindable {
	const TYPE_DATE = 0;
	const TYPE_DATE_TIME = 1;
	const TYPE_STRING = 2;
	const TYPE_INT = 3;
	const TYPE_FLOAT = 4;
	const TYPE_BOOL = 5;
	const TYPE_ARRAY = 6;
	const TYPE_FILE = 7;
	const TYPE_BINDABLE = 8;

	/**
	 * Permet d'assigner les attributs de l'objet et de validé les données
	 *
	 * @param array $data
	 *        	Données provenant de la requête HTTP
	 */
	public function bind(array $data): bool {
		return $this->fill($data) && $this->validate();
	}

	protected function validate(): bool {
		return true;
	}

	protected abstract function fill(array $data): bool;

	protected function bindObjectListField(string $fieldName, array $data, string $class, bool $required = false): bool {
		$this->$fieldName = null;
		$valid = true;

		try {
			if ($required && isset($data [$fieldName])) {
				$this->$fieldName = array ();
				foreach ( $data [$fieldName] as $itemData ) {
					$item = new $class();
					$valid = $item->bind($itemData) && $valid;
					$this->$fieldName [] = $item;
				}
			} else if (isset($data [$fieldName])) {
				$this->$fieldName = array ();
				foreach ( $data [$fieldName] as $itemData ) {
					$item = new $class();
					$valid = $item->bind($itemData) && $valid;
					$this->$fieldName [] = $item;
				}
			} else if ($required) {
				$valid = false;
			}
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			$valid = false;
		}
		return $valid;
	}

	protected function bindObjectField(string $fieldName, array $data, string $class, bool $required = false): bool {
		$this->$fieldName = null;
		$valid = true;

		try {
			if ($required && isset($data [$fieldName])) {
				$this->$fieldName = new $class();
				$valid = $this->$fieldName->bind($data [$fieldName]);
			} else if (isset($data [$fieldName])) {
				$this->$fieldName = new $class();
				$valid = $this->$fieldName->bind($data [$fieldName]);
			} else if ($required) {
				$valid = false;
			} else {
				$this->$fieldName = new $class();
			}
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			$valid = false;
		}
		return $valid;
	}

	protected function bindListField(string $fieldName, array $data, int $dataType, bool $required = false): bool {
		$this->$fieldName = array ();
		$valid = true;
		$present = false;

		try {
			if ($required && isset($data [$fieldName])) {
				$valid = true;
				$present = true;
			} else if (isset($data [$fieldName])) {
				$valid = true;
				$present = true;
			} else if ($required) {
				$valid = false;
			}

			if ($valid && $present) {
				$this->$fieldName = array ();
				foreach ( $data [$fieldName] as $item ) {
					$this->$fieldName [] = $this->parseValue($item, $dataType);
				}
			}
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			$valid = false;
		}
		return $valid;
	}

	protected function bindField(string $fieldName, array $data, int $dataType, bool $required = false): bool {
		$this->$fieldName = null;
		$valid = true;

		try {
			if ($required && isset($data [$fieldName])) {
				$this->$fieldName = $this->parseValue($data [$fieldName], $dataType);
				$valid = true;
			} else if (isset($data [$fieldName])) {
				$this->$fieldName = $this->parseValue($data [$fieldName], $dataType);
				$valid = true;
			} else if ($required) {
				$valid = false;
			}
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			$valid = false;
		}
		return $valid;
	}

	protected function bindFile(string $fieldName, array $data, array $allowedExt, int $fileSize, bool $required = false): bool {
		$this->$fieldName = null;
		$valid = true;

		try {
			if ($required && isset($data [$fieldName])) {
				$file = $data [$fieldName];
				FileUtils::checkFile($file, $allowedExt, $fileSize);
				$this->$fieldName = $file;
				$valid = ($file != null);
			} else if (isset($data [$fieldName])) {
				$file = $data [$fieldName];
				FileUtils::checkFile($file, $allowedExt, $fileSize);
				$this->$fieldName = $file;
				$valid = ($file != null);
			} else if ($required) {
				$valid = false;
			}
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			$valid = false;
		}
		return $valid;
	}

	/**
	 *
	 * @param mixed $value
	 * @param int $dataType
	 * @throws IllegalArgumentException
	 * @return mixed
	 */
	private function parseValue($value, int $dataType) {
		switch ($dataType) {
			case self::TYPE_DATE :
				return new \DateTime($value);
			case self::TYPE_DATE_TIME :
				return new \DateTime($value);
			case self::TYPE_INT :
				return intval($value);
			case self::TYPE_FLOAT :
				return floatval($value);
			case self::TYPE_STRING :
				return strval($value);
			case self::TYPE_BOOL :
				return $value == "true" || $value == "on";
			case self::TYPE_FILE :
				$uploaddir = Config::get(Config::UPLOAD_DIR);
				$fileName = basename($_FILES ['userfile'] ['name']);
				$uploadfile = $uploaddir . $fileName;
				if (move_uploaded_file($_FILES ['userfile'] ['tmp_name'], $uploadfile)) {
					return $fileName;
				} else {
					throw new IllegalArgumentException(Message::ERROR_UNEXPECTED);
				}
				return $value;
			default :
				throw new IllegalArgumentException(Message::get(Message::ERROR_BAD_FORMAT));
		}
	}
}