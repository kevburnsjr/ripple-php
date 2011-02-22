<?php

namespace Ripple\Test;

$testsets['document'] = array(

	"Save" => function($fxs) {
		$address = new Address('home');
		$address->state = "California";
		return $address->save();
	},

	"Find By Key" => function($fxs) {
		$address = Address::find('home');
		return $address instanceof Address;
	},

	"Update" => function($fxs) {
		$address = Address::find('home')
			->clear()
			->set('city', "San Francisco")
			->save();
		$address = Address::find('home');
		return isset($address->city) && $address->city == "San Francisco";
	},

	"Clear" => function($fxs) {
		$address1 = Address::create('home')
			->set('zip', 94025)
			->clear();
		return !isset($address1->zip);
	},
	
	"Reload" => function($fxs) {
		$address1 = Address::find('home')
			->clear()
			->save();
		$address2 = Address::find('home')
			->set('zip', 94025)
			->save();
		$address1->reload();
		return isset($address1->zip);
	},

	"Move To" => function($fxs) {
		$address = Address::find('home')
			->moveTo('home2');
		return !Address::find('home')->exists() && Address::find('home2')->exists();
	},

	"Delete" => function($fxs) {
		$address = Address::find('home2')
			->delete();
		return !Address::find('home2')->exists();
	},

	"Save Without Key" => function($fxs) {
		$address = Address::create()
			->set('state', "California")
			->save();
		$address->delete();
		return $address->exists();
	},
	
);
