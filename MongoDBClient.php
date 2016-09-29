<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MongoDB
 *
 * @author e
 */
class MongoDBClient extends MongoClient implements IDB {
	//put your code here

	public function collectionExists($collection) {

		if ($this->system->namespaces->findOne(array('name' => 'eI_db.'.$collection)) === null) {
			return false;
		}
		return true;

	}

	public static function getInstance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new self();
		}

		return $instance;
	}

	public function getDB($name) {
		return $this->selectDB($name);
	}

	public function setDB($name) {

	}

	public function insertItems($items, $db, $table) {
		$db = $this->getDB($db);

		if ($this->collectionExists($table) === null) {
			$collection = $db->createCollection($table);
		} else {
			$collection = $db->selectCollection($table);
		}

		foreach ($items as $k   => $v) {
			$q = array('productId' => $v->productId);
			if (!$collection->findOne($q)) {
				$collection->insert($v);
			}
		}
	}

	public function removeAll($db, $table) {
		$db         = $this->getDB($db);
		$collection = $db->selectCollection($table);
		$collection->remove();
	}

}
