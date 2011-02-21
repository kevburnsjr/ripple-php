<?php

namespace Ripple;

require_once('Document/Collection.class.php');

class Document extends \Ripple {

	private $_bucket;
	protected $_key = null;
	
	protected static $_collection_class = '\\Ripple\\Document\\Collection';
	protected static $_bucket_name;	
	protected static $_schema;
	
	public function __construct($data = array()) {
		if(is_array($data) && count($data)) {
			$this->update($data);
		}
	}

// Public Static methods
	
	public static function create($key, $data = array()) {		
		$model = new static($data);
		$model->setKey($key);
		return $model;
	}
	
	public static function find($key) {
		$bucket = parent::client()->bucket(static::bucket_name());
		$r = $bucket->get($key);
		if($r->exists) {
			return static::from($r);
		}
	}
	
	public static function all() {
		$collection = new static::$_collection_class();
		$bucket = parent::client()->bucket(static::bucket_name());
		$keys = $bucket->getKeys();
		foreach($keys as $key) {
			$r = $bucket->get($key);
			if($r->exists) {
				$model = static::from($r);
				$collection->push($model);
			}
		}
		return $collection;
	}
	
	// TODO:: make filtering chainable with limiting and other niceties.
 	public static function key_filter($key_filter) {
		$collection = new static::$_collection_class();
		$mapred = new \RiakMapReduce(parent::client());
		$mapred->inputs = array(
			'bucket' => static::$_bucket_name,
			'key_filters' => array($key_filter)
		);
		$links = $mapred->run();
		foreach($links as $link) {
			$r = $link->get();
			$collection->push(static::from($r));
		}
		return $collection;
	}

	public static function bucket_name() {
		if(isset(static::$_bucket_name)) {
			return static::$_bucket_name;
		} else {
			$class_parts = explode('\\', strtolower(get_class($this)));
			static::$_bucket_name = array_pop($class_parts);
		}
		return static::$_bucket_name;
	}
	
// Public Instance methods

	public function bucket($bucket = null) {
		if(isset($this->_bucket)) {
			return $this->_bucket;
		} else {
			$this->_bucket = $this->client()->bucket(static::bucket_name());
		}
		return $this->_bucket;
	}
	
	public function key() {
		return $this->_key;
	}

	public function setKey($key = null) {
		$this->_key = $key;
		return $this;
	}

	public function save() {
		$bucket = $this->bucket();
		$properties = $this->getPublicProperties();
		if($r = $bucket->newObject($this->_key, $properties)->store()) {
			$this->_key = $r->key;
			return $this;
		}
	}
	
	public function update($props) {
		$props = is_object($props) ? (array)$props : $props;
		if(is_array($props)) {
			foreach($props as $k => $v) {
				$this->$k = $v;
			}
			return $this;
		}
	}
	
	public function delete() {
		if($this->bucket()->get($this->_key)->delete()) {
			return $this;
		}
	}
	
	public function reload() {
		$bucket = $this->bucket();
		$r = $bucket->get($this->_key);
		return $this->update($r->data);
	}
	
	public function moveTo($new_key) {
		return $this->delete()->setKey($new_key)->save();
	}
	
// Protected Static methods

	protected static function from($r) {
		if($r instanceof \RiakObject) {
			$model = new static();
			$model->setKey($r->key);
			$model->update($r->data);
			return $model;
		}
	}
	
// Protected Methods
	
	protected function getPublicProperties() {
		$getPublicProperties = function($obj) { return get_object_vars($obj); };
		return $getPublicProperties($this);
	}

}