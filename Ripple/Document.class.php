<?php

namespace Ripple;

class Document extends \Ripple {
	
	private $bucket_name;
	private $bucket;

// Static methods
	
	public static function find() {
		$keys = func_get_args();
	}
	
// Instance methods

	public function bucket_name() {
		if(isset($this->bucket_name)) {
			return $this->bucket_name;
		} else if(isset(static::$bucket_name)) {
			$this->bucket_name = static::$bucket_name;
		} else {
			$class_parts = explode('\\', strtolower(get_class($this)));
			$this->bucket_name = array_pop($class_parts);
		}
		return $this->bucket_name;
	}

	public function bucket() {
		if(isset($this->bucket)) {
			return $this->bucket;
		} else {
			$this->bucket = $this->client()->bucket($this->bucket_name());
		}
		return $this->bucket;
	}

	public function save() {
	}
	
	public function update($props) {
	}
	
	public function delete($props) {
	}
	
// Private methods
	
	public function _key() {
		return strtolower();
	}

}