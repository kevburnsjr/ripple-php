<?php

namespace Ripple\Test;

$testsets['collection'] = array(

	"Instantiate Collection" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$addresses->push(Address::create('test', $fxs->address1));
		$addresses->push(Address::create('test2', $fxs->address1));
		$addresses->push(Address::create('test3', $fxs->address1));
		return count($addresses);
	},

	"Save" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$k1 = md5(rand());
		$k2 = md5(rand());
		$addresses->push(Address::create($k1, $fxs->address1));
		$addresses->push(Address::create($k2, $fxs->address1));
		$addresses->save();
		return Address::find($k1)->exists() && Address::find($k2)->exists();
	},

	"Delete" => function($fxs) {
		$addresses = new \Ripple\Document\Collection();
		$k1 = md5(rand());
		$k2 = md5(rand());
		$addresses->push(Address::create($k1, $fxs->address1));
		$addresses->push(Address::create($k2, $fxs->address1));
		$addresses->save()->delete();
		return !Address::find($k1)->exists() && !Address::find($k2)->exists();
	},
	
);
