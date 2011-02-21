<?php

namespace Ripple\Test;

$testsets['collection'] = array(

	"Instantiate Collection" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$data = array('junk' => "data");
		$addresses->push(Model\Address::create('test', $data));
		$addresses->push(Model\Address::create('test2', $data));
		$addresses->push(Model\Address::create('test3', $data));
		return count($addresses);
	},

	"Save" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$data = array('junk' => "data");
		$k1 = md5(rand());
		$k2 = md5(rand());
		$addresses->push(Model\Address::create($k1, $data));
		$addresses->push(Model\Address::create($k2, $data));
		$addresses->save();
		return Model\Address::find($k1) && Model\Address::find($k2);
	},

	"Delete" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$data = array('junk' => "data");
		$k1 = md5(rand());
		$k2 = md5(rand());
		$addresses->push(Model\Address::create($k1, $data));
		$addresses->push(Model\Address::create($k2, $data));
		$addresses->save()->delete();
		return !Model\Address::find($k1) && !Model\Address::find($k2);
	},
	
);
