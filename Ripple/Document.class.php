<?php

namespace Ripple;

require_once('Document/Collection.class.php');

/**
 * Document class for models to extend
 */
class Document {
	
	protected $_key;
	protected $_exists;
	protected $_links = array();
	protected $_headers = array();
	
	protected static $_collection_class = '\\Ripple\\Document\\Collection';
	protected static $_client;
	protected static $_bucket;
	protected static $_bucket_name;
	
	/**
	 * Construct a new Ripple document
	 * @param string $key - Key for this object
	 * @param mixed $props - Key for this object
	 */
	public function __construct($key = null, $props = array()) {
		$this->setKey($key);
		$this->update($props);
	}
	
// Public Static methods
	
	/**
	 * Convenience method for instantiation
	 * @see __construct()
	 * @param string $key - Key for this object
	 * @param mixed $props - Key for this object
	 * @return \\Ripple\\Document
	 */
	public static function create($key = null, $props = array()) {
		return new static($key, $props);
	}
	
	/**
	 * Find by Key
	 * @param string $key - Key to find
	 * @return \\Ripple\\Document - Returns Document or null
	 */
	public static function find($key) {
		$bucket = static::client()->bucket(static::bucket_name());
		$r = $bucket->get($key);
		if($r instanceof \RiakObject) {
			return static::from($r);
		}
	}
	
	/**
	 * Find all objects
	 * @return \\Ripple\\Document\Collection 
	 */
	public static function all() {
		$collection = new static::$_collection_class();
		$bucket = static::client()->bucket(static::bucket_name());
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
	
	/**
	 * Find all objects with keys that match the filter
	 * More Info: http://wiki.basho.com/Key-Filters.html
	 * @param array $key_filter - Key Filter ex: array("ends_with", "0603")
	 * @return \\Ripple\\Document\Collection 
	 */
 	public static function key_filter($key_filter) {
		$collection = new static::$_collection_class();
		$mapred = new \RiakMapReduce(static::client());
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
	
	/**
	 * Get a map reduce object over this bucket
	 * More Info: http://wiki.basho.com/MapReduce.html
	 * @return \\RiakMapReduce
	 */
 	public static function mapReduce() {
		$mapred = new \RiakMapReduce(static::client());
		return $mapred->add(static::_bucket()->name);
	}

// Protected static methods
	
	/**
	 * Get/set Bucket
	 * @param \RiakBucket $bucket - Map function
	 * @return \\RiakBucket
	 */
	protected static function _bucket($bucket = null) {
		if($bucket) {
			return static::$_bucket = $bucket;
		} else if(!isset(static::$_bucket)) {
			static::$_bucket = static::client()->bucket(static::bucket_name());
		}
		return static::$_bucket;
	}
	
	/**
	 * Get/set Client
	 * @param \RiakClient $client - Map function
	 * @return \\RiakClient
	 */
	protected static function client($client = null) {
		if($client) {
			return static::$_client = $client;
		} else if(!isset(static::$_client)) {
			static::$_client = new \RiakClient();
		}
		return static::$_client;
	}

	/**
	 * Get name for bucket to store objects
	 * @return string
	 */
	protected static function bucket_name() {
		if(isset(static::$_bucket_name)) {
			return static::$_bucket_name;
		} else {
			$class_parts = explode('\\', strtolower(get_class($this)));
			static::$_bucket_name = array_pop($class_parts);
		}
		return static::$_bucket_name;
	}
	
	/**
	 * Instantiates Document from Riak transaction response
	 * @param \RiakObject $r - Response with key and data to be copied
	 * @return \\Ripple\\Document - Returns new Document
	 */
	protected static function from($r) {
		if($r instanceof \RiakObject) {
			$model = new static($r->key, $r->data);
			$model->_links = $r->getLinks();
			$model->_exists = (bool)$r->exists;
			$model->_headers = $r->headers;
			return $model;
		}
	}
	
// Public Instance methods
	
	/**
	 * Get Bucket
	 * @return \\RiakBucket
	 */
	public function bucket() {
		return static::_bucket();
	}
	
	/**
	 * Get key for this Document
	 * @return string
	 */
	public function key() {
		return $this->_key;
	}
	
	/**
	 * Does this record exist?
	 * @return boolean
	 */
	public function exists() {
		return $this->_exists;
	}
	
	/**
	 * Sets key for this document
	 * @param string $key
	 * @return \\Ripple\\Document - Returns self
	 */
	public function setKey($key = null) {
		$this->_key = $key;
		return $this;
	}
	
	/**
	 * Saves this document to the bucket
	 * @return \\Ripple\\Document - Returns self or null
	 */
	public function save() {
		$bucket = $this->bucket();
		$properties = $this->getPublicProperties();
		$r = $bucket->newObject($this->_key, $properties);
		$r->links = $this->_links;
		if(isset($this->_headers['x-riak-vclock'])) {
			$r->headers['x-riak-vclock'] = $this->_headers['x-riak-vclock'];
		}
		if($r->store()) {
			$this->_key = $r->key;
			$this->_exists = (bool)$r->exists;
			$this->_headers = $r->headers;
			return $this;
		}
	}
	
	/**
	 * Updates properties of this document with members of supplied array
	 * @param mixed $props - Array or object containing properties to be copied 
	 * @return \\Ripple\\Document - Returns self
	 */
	public function update($props) {
		$props = is_object($props) ? (array)$props : $props;
		if(is_array($props)) {
			foreach($props as $k => $v) {
				$this->$k = $v;
			}
		}
		return $this;
	}
	
	/**
	 * Deletes this document from the bucket
	 * @return \\Ripple\\Document - Returns self or null
	 */
	public function delete() {
		if($r = $this->bucket()->get($this->_key)->delete()) {
			return $this;
		}
	}
	
	/**
	 * Reloads this document from the bucket
	 * @return \\Ripple\\Document - Returns self
	 */
	public function reload() {
		$bucket = $this->bucket();
		$r = $bucket->get($this->_key);
		return $this->update($r->data);
	}
	
	/**
	 * Clears properties and links on this document
	 * @return \\Ripple\\Document - Returns self
	 */
	public function clear() {
		$bucket = $this->bucket();
		$props = $this->getPublicProperties();
		foreach($props as $k => $v) {
			unset($this->$k);
		}
		return $this;
	}
	
	/**
	 * Reloads this document to another key
	 * @param string $new_key 
	 * @return \\Ripple\\Document - Returns self
	 */
	public function moveTo($new_key) {
		return $this->delete()->setKey($new_key)->save();
	}
	
	/**
	 * Sets a key on this object to a given value
	 * Also works with arrays of key => value pairs
	 * @param mixed $k - string or array of $k => $v pairs
	 * @param mixed $v - value or null
	 * @return \\Ripple\\Document - Returns self
	 */
	public function set($k, $v = null) {
		if(is_array($k)) {
			foreach($k as $k1 => $v1) {
				$this->set($k1, $v1);
			}
		} else if(substr($k,0,1) != '_') {
			$this->$k = $v;
		}
		return $this;
	}
	
	/**
	 * Adds a link from this Document to another Document
	 * @param mixed $doc - Link or Document representing link destination
	 * @param string $tag - Tag for link (ex: friend)
	 * @return \\Ripple\\Document - Returns self
	 */
	public function addLink($doc, $tag = null) {
		if($doc instanceof \RiakLink) {
			$new_link = $doc;
		} else if($doc instanceof \Ripple\Document) {
			$new_link = new \RiakLink($doc->bucket()->name, $doc->key(), $tag);
		}
		$this->_links[] = $new_link;
		return $this;
	}
	
	/**
	 * Sets a link from this Document to another Document for a given tag
	 * Used for one-to-one relationships 
	 * @param mixed $doc - Link or Document representing link destination
	 * @param string $tag - Tag for link (ex: friend)
	 * @return \\Ripple\\Document - Returns self
	 */
	public function setLink($doc, $tag) {
		if($doc instanceof \RiakLink) {
			$new_link = $doc;
		} else if($doc instanceof \Ripple\Document) {
			$new_link = new \RiakLink($doc->bucket()->name, $doc->key(), $tag);
		}
		foreach($this->_links as $i => $link) {
			if($link->getTag() == $tag) {
				unset($this->_links[$i]);
			}
		}
		$this->_links[] = $new_link;
		return $this;
	}
	
	/**
	 * Sets a link from this Document to another Document for a given tag
	 * Used for one-to-one relationships 
	 * @param mixed $doc - Link or Document representing link destination
	 * @param string $tag - Tag for link (ex: friend)
	 * @return \\Ripple\\Document - Returns self
	 */
	public function removeLink($doc, $tag = null) {
		if($doc instanceof \RiakLink) {
			$rem_link = $doc;
		} else if($doc instanceof \Ripple\Document) {
			$rem_link = new \RiakLink($doc->bucket()->name, $doc->key(), $tag);
		}
		foreach($this->_links as $i => $link) {
			if($link->isEqual($rem_link)) {
				unset($this->_links[$i]);
			}
		}
		return $this;
	}
	
// Protected Methods
	
	/**
	 * Returns values of all public properties for creating transport object
	 * This is a crutch for documents that don't have a schema
	 * @return array - Returns array of public properties and values
	 */
	protected function getPublicProperties() {
		$getPublicProperties = function($obj) { return get_object_vars($obj); };
		return $getPublicProperties($this);
	}
	
}
