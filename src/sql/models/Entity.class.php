<?php
namespace phpessential\mvc\sql\models;

use phpessential\mvc\Config;
use phpessential\mvc\exception\IllegalAccessException;
use phpessential\mvc\sql\core\DbConnection;
use phpessential\mvc\sql\core\metadata\AbstractAssociation;
use phpessential\mvc\sql\core\metadata\Field;
use phpessential\mvc\sql\core\metadata\ManyToMany;
use phpessential\mvc\sql\core\metadata\ManyToOne;
use phpessential\mvc\sql\core\metadata\OneToMany;
use phpessential\mvc\sql\core\metadata\OneToOne;
use phpessential\mvc\sql\core\metadata\OneToOneRef;
use phpessential\mvc\sql\core\metadata\TableEntity;
use phpessential\mvc\sql\core\query\AliasedTable;
use phpessential\mvc\sql\core\query\Delete;
use phpessential\mvc\sql\core\query\Insert;
use phpessential\mvc\sql\core\query\QueryField;
use phpessential\mvc\sql\utils\SqlUtils;
use phpessential\mvc\utils\LogUtils;
use phpessential\mvc\sql\core\metadata\AbstractField;
use phpessential\mvc\sql\core\query\Update;

/**
 * Representation of datable row,
 */
class Entity extends AbstractEntity {

	function __construct(array $data = array()) {
		parent::__construct($data);
	}

	/**
	 * Return discriminant fields of model
	 *
	 * @return Field[]
	 */
	public static function discriminantFields() {
		throw new IllegalAccessException("Unimplemented function: " . static::class . "->distinctFields()");
	}

	/**
	 * Return database table link to this model
	 *
	 * @return TableEntity
	 */
	public static function table(): TableEntity {
		throw new IllegalAccessException("Unimplemented function: " . static::class . "->table()");
	}

	/**
	 * Mapping between database fields and model attributes
	 *
	 * @return AbstractField[]
	 */
	protected static function fields() {
		return static::discriminantFields();
	}

	/**
	 * Use to set the value of discriminant attributes after insert
	 *
	 * @param $conn DbConnection
	 */
	protected function setDiscriminantValue(DbConnection $conn) {
		throw new IllegalAccessException("Unimplemented function: " . static::class . "->setDiscriminantValues()");
	}

	/**
	 * Insert entity into database
	 *
	 * @param DbConnection $conn
	 * @param bool $activeTransaction
	 *
	 * @throws \Exception
	 */
	public function insert(DbConnection $conn = null) {
		if ($conn === null) {
			$conn = DbConnection::getInstance();
		}

		$endTransaction = false;
		if (! $conn->hasActiveTransaction()) {
			$conn->startTransaction();
			$endTransaction = true;
		}

		$fields = static::getFields();
		$entityFields = array ();

		foreach ( $fields as $field ) {
			if ($field instanceof Field) {
				$fieldName = $field->name;
				$value = $this->$fieldName;
				$entityFields [$field->sqlName] = array (
						$field,$value
				);
			} else if ($field instanceof ManyToOne || $field instanceof OneToOneRef) {
				$fieldName = $field->name;
				$foreignEntity = $this->$fieldName;

				foreach ( $field->linkFields as $linkField ) {
					if ($foreignEntity === null) {
						$value = null;
					} else {
						$foreignFieldName = $linkField->referenceField->name;
						$value = $foreignEntity->$foreignFieldName;
					}
					$entityFields [$linkField->sqlName] = array (
							$linkField->referenceField,$value
					);
				}
			}
		}

		try {
			$conn->execute((new Insert(static::table(), $entityFields))->toString(Config::get(Config::DB_NAME)));
			$this->setDiscriminantValue($conn);

			foreach ( $fields as $field ) {
				if (! ($field instanceof AbstractAssociation))
					continue;
				if ($field instanceof ManyToMany) {
					if (! $field->cascadeAssociation)
						continue;
				} else if (! $field->cascade)
					continue;

				if ($field instanceof ManyToMany) {
					$fieldName = $field->name;
					$entityList = $this->$fieldName;
					if (is_array($entityList)) {
						$entityFields = array ();
						foreach ( $entityList as $entity ) {
							foreach ( $field->linkFields as $linkField ) {
								$linkFieldName = $linkField->referenceField->name;
								$entityFields [$linkField->sqlName] = array (
										$linkField->referenceField,$entity->$linkFieldName
								);
							}
							foreach ( $field->primaryFields as $linkField ) {
								$linkFieldName = $linkField->referenceField->name;
								$entityFields [$linkField->sqlName] = array (
										$linkField->referenceField,$this->$linkFieldName
								);
							}
							$conn->execute((new Insert($field->associativeTable, $entityFields))->toString(Config::get(Config::DB_NAME)));
						}
					}
				} else if ($field instanceof OneToMany) {
					$fieldName = $field->name;
					$entityList = $this->$fieldName;
					if (is_array($entityList)) {
						$referenceFieldName = $field->referenceField->name;
						foreach ( $entityList as $entity ) {
							$entity->$referenceFieldName = $this;
							$entity->insert($conn);
						}
					}
				} else if ($field instanceof OneToOne) {
					$fieldName = $field->name;
					$entity = $this->$fieldName;
					if ($entity instanceof Entity) {
						$referenceFieldName = $field->referenceField->name;
						$entity->$referenceFieldName = $this;
						$entity->insert($conn);
					}
				}
			}
		} catch ( \Exception $e ) {
			if ($endTransaction) {
				$conn->rollback();
			}
			LogUtils::error($e);
			throw $e;
		}

		if ($endTransaction) {
			$conn->commit();
		}
	}

	/**
	 * Permet de mettre à jour un model en base de données
	 *
	 * @param
	 *        	DbConnection
	 * @throws \Exception
	 */
	public function update(DbConnection $conn = null) {
		if ($conn === null) {
			$conn = DbConnection::getInstance();
		}

		$endTransaction = false;
		if (! $conn->hasActiveTransaction()) {
			$conn->startTransaction();
			$endTransaction = true;
		}

		$fields = static::getFields();
		$entityFields = array ();

		foreach ( $fields as $field ) {
			if ($field instanceof Field) {
				$fieldName = $field->name;
				$value = $this->$fieldName;
				$entityFields [$field->sqlName] = array (
						$field,$value
				);
			} else if ($field instanceof ManyToOne || $field instanceof OneToOneRef) {
				$fieldName = $field->name;
				$foreignEntity = $this->$fieldName;

				foreach ( $field->linkFields as $linkField ) {
					if ($foreignEntity === null) {
						$value = null;
					} else {
						$foreignFieldName = $linkField->referenceField->name;
						$value = $foreignEntity->$foreignFieldName;
					}
					$entityFields [$linkField->sqlName] = array (
							$linkField->referenceField,$value
					);
				}
			}
		}

		try {
			$table = static::table();
			$update = new Update($table, $entityFields);
			$aliasedTable = new AliasedTable($table->getName(), "", $table);
			foreach (static::discriminantFields() as $discriminant) {
				$fieldName = $discriminant->name;
				$update->conditions->eq(new QueryField($aliasedTable, $discriminant), $this->$fieldName);
			}
			$conn->execute($update->toString(Config::get(Config::DB_NAME)));

			foreach ( $fields as $field ) {
				if (! ($field instanceof AbstractAssociation))
					continue;
				if (($field instanceof ManyToMany)) {
					if (! $field->cascadeAssociation)
						continue;
				} else if (! $field->cascade)
					continue;

				if ($field instanceof ManyToMany) {
					$fieldName = $field->name;
					$entityList = $this->$fieldName;
					// TODO: Find way to detect and identify modification instead of deleted old and insert new
					// First delete old associations
					$associationDelete = new Delete($field->associativeTable);
					foreach ( $field->primaryFields as $linkField ) {
						$linkFieldName = $linkField->referenceField->name;
						$associationDelete->conditions->eq(new QueryField(new AliasedTable($field->associativeTable->getName(), "", $field->associativeTable), new Field($linkFieldName, $linkField->sqlName, $linkField->referenceField->dataType)), $this->$linkFieldName);
					}
					$conn->execute($associationDelete->toString(Config::get(Config::DB_NAME)));

					// And insert new ones
					if (is_array($entityList)) {
						$entityFields = array ();
						foreach ( $entityList as $entity ) {
							foreach ( $field->linkFields as $linkField ) {
								$linkFieldName = $linkField->referenceField->name;
								$entityFields [$linkField->sqlName] = array (
										$linkField->referenceField,$entity->$linkFieldName
								);
							}
							foreach ( $field->primaryFields as $linkField ) {
								$linkFieldName = $linkField->referenceField->name;
								$entityFields [$linkField->sqlName] = array (
										$linkField->referenceField,$this->$linkFieldName
								);
							}
							$conn->execute((new Insert($field->associativeTable, $entityFields))->toString(Config::get(Config::DB_NAME)));
						}
					}
				} else if ($field instanceof OneToMany) {
					$fieldName = $field->name;
					$entityList = $this->$fieldName;
					if (is_array($entityList)) {
						$referenceFieldName = $field->referenceField->name;
						foreach ( $entityList as $entity ) {
							$entity->$referenceFieldName = $this;
							$entity->update($conn);
						}
					}
				} else if ($field instanceof OneToOne) {
					$fieldName = $field->name;
					$entity = $this->$fieldName;
					if ($entity instanceof Entity) {
						$referenceFieldName = $field->referenceField->name;
						$entity->$referenceFieldName = $this;
						$entity->update($conn);
					}
				}
			}
		} catch ( \Exception $e ) {
			if ($endTransaction) {
				$conn->rollback();
			}
			LogUtils::error($e);
			throw $e;
		}

		if ($endTransaction) {
			$conn->commit();
		}
	}

	/**
	 * Delete entity from database
	 *
	 * @param DbConnection $conn
	 * @param bool $activeTransaction
	 *
	 * @throws \Exception
	 */
	public function delete(DbConnection $conn = null) {
		if ($conn === null) {
			$conn = DbConnection::getInstance();
		}

		$endTransaction = false;
		if (! $conn->hasActiveTransaction()) {
			$conn->startTransaction();
			$endTransaction = true;
		}

		$fields = static::getFields();
		$entityFields = array ();

		try {
			foreach ( $fields as $field ) {
				if (! ($field instanceof AbstractAssociation))
					continue;
				if ($field instanceof ManyToMany) {
					if ($field->cascadeAssociation) {
						$secondaryDelete = new Delete($field->associativeTable);
						foreach ( $field->primaryFields as $linkField ) {
							$linkFieldName = $linkField->referenceField->name;
							$secondaryDelete->conditions->eq(new QueryField(new AliasedTable($field->associativeTable->getName(), "", $field->associativeTable), new Field($linkFieldName, $linkField->sqlName, $linkField->referenceField->dataType)), $this->$linkFieldName);
						}
						$conn->execute($secondaryDelete->toString(Config::get(Config::DB_NAME)));
					} else {
						// TODO: Update with null
					}
					if ($field->cascade) {
						$fieldName = $field->name;
						$entityList = $this->$fieldName;
						foreach ( $entityList as $entity ) {
							$entity->delete($conn);
						}
					}
				} else if ($field instanceof OneToMany) {
					$fieldName = $field->name;
					$entityList = $this->$fieldName;
					if (is_array($entityList)) {
						if ($field->cascade) {
							foreach ( $entityList as $entity ) {
								$entity->delete($conn);
							}
						} else {
							// TODO: Update with null
						}
					}
				} else if ($field instanceof OneToOne) {
					$fieldName = $field->name;
					$entity = $this->$fieldName;
					if ($entity instanceof Entity) {
						if ($field->cascade) {
							$entity->delete($conn);
						} else {
							// TODO: Update with null
						}
					}
				}
			}

			$primaryTable = static::table();
			$primaryDelete = new Delete($primaryTable);
			$primaryDiscriminants = static::discriminantFields();
			foreach ( $primaryDiscriminants as $discriminant ) {
				$fieldName = $discriminant->name;
				$primaryDelete->conditions->eq(new QueryField(new AliasedTable($primaryTable->getName(), "", $primaryTable), $discriminant), $this->$fieldName);
			}
			$conn->execute($primaryDelete->toString(Config::get(Config::DB_NAME)));
		} catch ( \Exception $e ) {
			if ($endTransaction) {
				$conn->rollback();
			}
			LogUtils::error($e);
			throw $e;
		}

		if ($endTransaction) {
			$conn->commit();
		}
	}

	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param Field $field
	 * @param Entity $entity
	 * @throws \Exception
	 * @return string
	 */
	private function convertEntityToSqlValue(Field $field): string {
		$fieldName = $field->name;

		if ($field instanceof ManyToOne) {
			// TODO: manage multiple reference fields
			$foreignFieldName = $field->linkFields [0]->sqlName;
			$entityValue = $this->$fieldName->$foreignFieldName;
		} else {
			$entityValue = $this->$fieldName;
		}

		return SqlUtils::addQuote($field, SqlUtils::ensureSqlValue($field, $entityValue));
	}

	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param ManyToOne $field
	 * @param Entity $entity
	 * @throws \Exception
	 * @return string[]
	 */
	private function convertForeignEntityToSqlValues(ManyToOne $field, Entity $entity): array {
		$fieldName = $field->name;

		$result = array ();
		foreach ( $field->linkFields as $referentField ) {
			$result [$referentField->sqlName] = $this->convertEntityToSqlValue($referentField, $entity->$fieldName);
		}
		return $result;
	}
}