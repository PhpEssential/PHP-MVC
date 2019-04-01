<?php
namespace tse\mvc\ui\form;

use tse\mvc\exception\IllegalArgumentException;
use tse\mvc\i18n\Message;
use tse\mvc\utils\FileUtils;
use tse\mvc\utils\LogUtils;
use tse\mvc\utils\StringUtils;

/**
 * You can bind an object with data of an http request if it inherit this class
 */
abstract class Bindable {
	const TYPE_DATE = 0;
	const TYPE_DATE_TIME = 1;
	const TYPE_STRING = 2;
	const TYPE_INT = 3;
	const TYPE_FLOAT = 4;
	const TYPE_BOOL = 5;

	/**
	 * Start loading of bindable object's attributes
	 *
	 * @param array $data
	 *        	- Data from http request $_POST or $_GET or $_FILE or $_POST + $_FILE or ...
	 * @return bool
	 */
	public function bind(array $data): bool {
		return $this->fill($data) && $this->validate();
	}

	/**
	 * Validation of loaded attribute values
	 *
	 * @return bool
	 */
	protected function validate(): bool {
		return true;
	}

	/**
	 * Load attributes of the bindable object
	 *
	 * @param array $data
	 *        	- Data from http request $_POST or $_GET or $_FILE or $_POST + $_FILE or ...
	 * @return bool
	 */
	protected abstract function fill(array $data): bool;

	protected function bindObjectListField(string $fieldName, array $data, string $class, $default = array()): bool {
		$valid = true;
		if (isset($data [$fieldName])) {
			try {
				$this->$fieldName = array ();
				foreach ( $data [$fieldName] as $itemData ) {
					$item = new $class();
					$valid = $item->bind($itemData) && $valid;
					$this->$fieldName [] = $item;
					if (! $valid)
						break;
				}
			} catch ( \Exception $e ) {
				$valid = $this->handleError($e, $fieldName);
			}
		} else {
			$this->$fieldName = $default;
		}
		return $valid;
	}

	/**
	 * Bind object attribute from request
	 *
	 * @param string $fieldName
	 *        	- Name of field to load
	 * @param array $data
	 *        	- Data from http request $_POST or $_GET or $_FILE or $_POST + $_FILE or ...
	 * @param string $class
	 *        	- Class of object to load
	 * @param mixed $default
	 *        	- Default value (null by default)
	 *
	 * @return bool - TRUE if no problem
	 */
	protected function bindObjectField(string $fieldName, array $data, string $class, $default = null): bool {
		$valid = true;
		if (isset($data [$fieldName])) {
			$reducedData = $data [$fieldName];
		} else {
			$reducedData = array_reduce(array_keys($data), function ($acc, $item) use ($fieldName, $data) {
				if (StringUtils::startsWith($item, $fieldName . "_")) {
					$acc [substr($item, strlen($fieldName) + 1)] = $data [$item];
				}
				return $acc;
			}, array ());
		}

		if (! empty($reducedData)) {
			try {
				$this->$fieldName = new $class();
				$valid = $this->$fieldName->bind($reducedData);
			} catch ( \Exception $e ) {
				$valid = $this->handleError($e, $fieldName);
			}
		} else {
			$this->$fieldName = $default;
		}
		return $valid;
	}

	/**
	 * Bind list of primitive attribute from request
	 *
	 * @param string $fieldName
	 *        	- Name of field to load
	 * @param array $data
	 *        	- Data from http request $_POST or $_GET or $_FILE or $_POST + $_FILE or ...
	 * @param string $dataType
	 *        	- Data type
	 * @param mixed $default
	 *        	- Default value (array() by default)
	 *
	 * @return bool - TRUE if no problem
	 */
	protected function bindListField(string $fieldName, array $data, int $dataType, $default = array()): bool {
		$valid = true;
		if (isset($data [$fieldName])) {
			try {
				$this->$fieldName = array ();
				foreach ( $data [$fieldName] as $item ) {
					$this->$fieldName [] = $this->parseValue($item, $dataType);
				}
			} catch ( \Exception $e ) {
				$valid = $this->handleError($e, $fieldName);
			}
		} else {
			$this->$fieldName = $default;
		}
		return $valid;
	}

	/**
	 * Bind primitive attribute from request
	 *
	 * @param string $fieldName
	 *        	- Name of field to load
	 * @param array $data
	 *        	- Data from http request $_POST or $_GET or $_FILE or $_POST + $_FILE or ...
	 * @param string $dataType
	 *        	- Data type
	 * @param mixed $default
	 *        	- Default value (null by default)
	 *
	 * @return bool - TRUE if no problem
	 */
	protected function bindField(string $fieldName, array $data, int $dataType, $default = null): bool {
		$valid = true;
		if (isset($data [$fieldName])) {
			try {
				$this->$fieldName = $this->parseValue($data [$fieldName], $dataType);
			} catch ( \Exception $e ) {
				$valid = $this->handleError($e, $fieldName);
			}
		} else {
			$this->$fieldName = $default;
		}

		return $valid;
	}

	/**
	 * Bind file attribute from request
	 *
	 * @param string $fieldName
	 *        	- Name of field to load
	 * @param array $data
	 *        	- Data from http request $_FILE or $_POST + $_FILE or ...
	 * @param string $allowedExt
	 *        	- Allowed MIME types
	 * @param mixed $fileSize
	 *        	- Max allowed file size
	 *
	 * @return bool - TRUE if no problem
	 */
	protected function bindFile(string $fieldName, array $data, array $allowedExt, int $fileSize): bool {
		$valid = true;
		if (isset($data [$fieldName])) {
			try {
				$file = $data [$fieldName];
				FileUtils::checkFile($file, $allowedExt, $fileSize);
				$this->$fieldName = $file;
				$valid = ($file != null);
			} catch ( \Exception $e ) {
				$valid = $this->handleError($e, $fieldName);
			}
		} else {
			$this->$fieldName = null;
		}
		return $valid;
	}

	/**
	 * Parse value from http request
	 *
	 * @param mixed $value
	 *        	- Value from request
	 * @param int $dataType
	 *        	- Data type
	 *
	 * @throws IllegalArgumentException - If unable to parse request data
	 *
	 * @return mixed - The parsed data
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
			default :
				throw new IllegalArgumentException(Message::get(Message::ERROR_BAD_FORMAT));
		}
	}

	/**
	 * Handle binding error
	 *
	 * @param \Exception $e
	 *        	- Thrown exception
	 * @param string $fieldName
	 *        	- Current loaded field
	 *
	 * @return bool FALSE
	 */
	private function handleError(\Exception $e, string $fieldName): bool {
		LogUtils::error($e);
		$this->$fieldName = null;
		return false;
	}
}