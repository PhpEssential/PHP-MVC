<?php
namespace phpessential\mvc\sql\dao;

use phpessential\mvc\Config;
use phpessential\mvc\sql\core\query\Fetcher;
use phpessential\mvc\sql\models\Entity;
use phpessential\mvc\sql\models\GenericEntity;

/**
 * Dao which work with GenericEntity
 */
abstract class GenericDao {

	/**
	 * Class of the model
	 *
	 * @var Entity
	 */
	protected $entityClass;

	/**
	 *
	 * @param string $entityClass
	 */
	function __construct(string $entityClass) {
		$this->entityClass = $entityClass;
	}

	/**
	 * Insert entity data into the corresponding table of database
	 *
	 * @param Entity $entity
	 * @param bool $activeTransaction
	 * @throws \Exception
	 */
	public function insert(Entity $entity) {
		$entity->insert();
	}

	/**
	 * Permet de mettre à jour un model en base de données
	 *
	 * @param Entity $entity
	 *        	model à mettre à jour
	 * @throws \Exception
	 */
	public function update(Entity $entity) {
		$entity->update();
	}

	/**
	 * Permet de supprimer un model en base de données
	 *
	 * @param Entity $entity
	 *        	model à supprimer
	 * @throws \Exception
	 */
	public function delete($entity) {
		$entity->delete();
	}

	/**
	 * Permet de supprimer un model en base de données
	 *
	 * @param
	 *        	int id du model à supprimer
	 * @throws \Exception
	 */
	public function deleteById(int $id) {
		$this->conn->execute("DELETE FROM " .
				Config::get(Config::DB_NAME) .
				"." .
				$this->modelClass::TABLE_NAME .
				" WHERE id=" .
				$id);
	}

	/**
	 * Permet d'exécuter la requête fournie par le SqlSelect
	 *
	 * @param Fetcher $select
	 *        	requête à exécuter
	 * @throws \Exception
	 * @return array
	 */
	public function selectRaw(Fetcher $select): array {
		$result = array ();
		try {
			$result = $this->conn->execute($select->toString(Config::get(Config::DB_NAME)));
		} catch ( \Exception $e ) {
			throw $e;
		}

		return $result;
	}

	public function defaultFetcher(Fetcher $fetcher = null): Fetcher {
		return $fetcher == null ? new Fetcher($this->entityClass) : $fetcher;
	}

	/**
	 *
	 * @param int $id
	 *
	 * @return GenericEntity
	 */
	public function findById(int $id) {
		$fetcher = $this->defaultFetcher();
		$fetcher->findConditions()->eq($fetcher->findField(GenericEntity::FIELD_ID), $id);
		return $fetcher->findUnique();
	}
}