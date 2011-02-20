<?php

namespace Ripple\Test;

$testsets['document'] = array(

	"Instantiate Model" => function($fxs) {
		$model = new \Ripple\Test\Model\Address();
		return $model instanceof \Ripple\Document;
	},

	"Bucket Name" => function($fxs) {
		$model = new \Ripple\Test\Model\Address();
		return $model->bucket_name() == 'address';
	},

	"Bucket" => function($fxs) {
		$model = new \Ripple\Test\Model\Address();
		$bucket = $model->bucket();
		return $bucket instanceof \RiakBucket && $bucket->name == 'address';
	},

	"Bucket" => function($fxs) {
		$model = new \Ripple\Test\Model\Address();
		$bucket = $model->bucket();
		return $bucket instanceof \RiakBucket && $bucket->name == 'address';
	},
	
/* 
	"Find Model" => function($fxs) {
		$model = \Ripple\Test\Model\Address::find('asdf');
		return $model instanceof \Ripple\Document;
	}, 
*/
	
);
