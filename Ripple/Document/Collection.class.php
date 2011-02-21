<?php

namespace Ripple\Document;

class Collection implements \ArrayAccess, \Countable {
	
	private $documents = array();
	private $hydrated = false;
	
	public static function rand($limit = 1) {
		return array_rand($this->documents, $limit);
	}
	
	public function push($document) {
		if($document instanceof \Ripple\Document) {
			$this->documents[$document->key()] = $document;
		} else if(is_array($document)) {
			foreach($document as $d) {
				$this->push($d);
			}
		}
		return $this;
	}
	
	// TODO: optimize this to save multiple in single request?
	// Does Riak even support bulk requests?
	public function save() {
		foreach($this->documents as $doc) {
			$doc->save();
		}
		return $this;
	}
	
	// TODO: optimize this to delete multiple in single request?
	// Does Riak even support bulk requests?
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