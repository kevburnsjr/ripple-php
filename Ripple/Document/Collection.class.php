<?php

namespace Ripple\Document;

class Collection implements \ArrayAccess, \Countable {
	
	private $documents = array();
	private $hydrated = false;
	
	/**
	 * Get some random members of this collection
	 * @param string $limit - Number of members to return
	 * @return mixed - Returns Document or Document Collection
	 */
	public function rand($limit = 1) {
		$docs = array_rand($this->documents, $limit);
		if(is_array($collection)) {
			$collection = new static();
			return $collection->push($docs);
		} else {
			return $docs;
		}
	}
	
	/**
	 * Push some documents into the collection
	 * @param mixed $documents - Document or array of Documents
	 * @return \Ripple\Document\Collection - Return self
	 */
	public function push($documents) {
		if($documents instanceof \Ripple\Document) {
			$this->documents[$documents->key()] = $documents;
		} else if(is_array($documents)) {
			foreach($documents as $d) {
				$this->push($d);
			}
		}
		return $this;
	}
	
	/**
	 * Save all documents in this collection
	 * @return \Ripple\Document\Collection - Return self
	 */
	public function save() {
		foreach($this->documents as $doc) {
			$doc->save();
		}
		return $this;
	}
	
	/**
	 * Save all documents in this collection
	 * @return \Ripple\Document\Collection - Return self
	 */
	public function delete() {
		foreach($this->documents as $doc) {
			$doc->delete();
		}
		return $this;
	}
	
	// Required ArrayAcces interface methods
	public function offsetExists($offset) {
		$this->hydrate();
		return isset($this->documents[$offset]);
	}
	public function offsetGet($offset) {
		$this->hydrate();
		if($this->offsetExists($offset)) {
			return $this->documents[$offset];
		}
	}
	public function offsetSet($offset, $value) {
		$this->documents[$offset] = $value;
	}
	public function offsetUnset($offset) {
		unset($this->documents[$offset]);
	}
	
	// Required Countable interface methods
	public function count() {
		$this->hydrate();
		return count($this->documents);
	}
	
// Protected Methods
	
	protected function hydrate() {
		if(!$this->hydrated) {
			// Generate request, execute and hydrate collection
		}
	}

}