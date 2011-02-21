<?php

namespace Ripple\Test;

$testsets['document'] = array(

	"Instantiate Model" => function($fxs) {
		$model = new Model\Address();
		return $model instanceof \Ripple\Document;
	},

	"Get Bucket Name" => function($fxs) {
		$model = new Model\Address();
		return $model->bucket_name() == 'ripple_test_addresses';
	},

	"Get Bucket" => function($fxs) {
		$model = new Model\Address();
		$bucket = $model->bucket();
		return $bucket instanceof \RiakBucket && $bucket->name == 'ripple_test_addresses';
	},

	"Create" => function($fxs) {
		$address = Model\Address::create('test');
		return $address instanceof \Ripple\Document;
	},

	"Save" => function($fxs) {
		$address = new Model\Address();
		$address->state = "California";
		$address->setKey('home');
		return $address->save();
	},

	"Find By Key" => function($fxs) {
		$address = Model\Address::find('home');
		return $address instanceof Model\Address;
	},

	"Update" => function($fxs) {
		$address = Model\Address::find('home');
		$address->update(array(
			'city' => "San Francisco"
		));
		$address->save();
		$address = Model\Address::find('home');
		return $address->city == "San Francisco";
	},

	"Reload" => function($fxs) {
		$address1 = Model\Address::find('home');
		$address2 = Model\Address::find('home');
		$address1->zip = 94025;
		$address1->save();
		$address2->reload();
		return isset($address2->zip) && $address1->zip == $address2->zip;
	},

	"Move To" => function($fxs) {
		$address = Model\Address::find('home');
		$address->moveTo('home2');
		return !Model\Address::find('home') && Model\Address::find('home2');
	},

	"Delete" => function($fxs) {
		$address = Model\Address::find('home2');
		return $address->delete();
	},

	"Save Without Key" => function($fxs) {
		$address = new Model\Address();
		$address->state = "California";
		$success = $address->save();
		$address->delete();
		return $success;
	},
	
);
