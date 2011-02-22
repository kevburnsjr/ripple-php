<?php

namespace Ripple\Test;

$testsets['doc 2 collection'] = array(

	"All" => function($fxs) {
		$data = array('junk' => "data");
		Address::create('test', $data)->save();
		Address::create('test2', $data)->save();
		Address::create('test3')->save();
		$addresses = Address::all();
		return count($addresses);
	},

	"Key Filter" => function($fxs) {
		$data = array('junk' => "data");
		Address::create('test', $data)->save();
		Address::create('test2', $data)->save();
		Address::create('test3')->save();
		$addresses = Address::key_filter(array('starts_with','test'));
		return count($addresses);
	},

	"Map" => function($fxs) {
		$function = "function (v) { return [v.key]; }";
		$data = array('junk' => "data");
		Address::create('test', $data)->save();
		Address::create('test2', $data)->save();
		$addresses = Address::mapReduce()->map($function)->run();
		return count($addresses);
	},
	
);
