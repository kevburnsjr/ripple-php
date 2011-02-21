<?php

namespace Ripple\Test;

$testsets['doc 2 collection'] = array(

	"Document: All" => function($fxs) {
		$data = array('junk' => "data");
		$address = Model\Address::create('test', $data)->save();
		$address = Model\Address::create('test2', $data)->save();
		$address = Model\Address::create('test3')->save();
		$addresses = Model\Address::all();
		return count($addresses);
	},

	"Document: Filter" => function($fxs) {
		$data = array('junk' => "data");
		$address = Model\Address::create('test', $data)->save();
		$address = Model\Address::create('test2', $data)->save();
		$address = Model\Address::create('test3')->save();
		$addresses = Model\Address::filter(array('starts_with','test'));
		return count($addresses);
	},
	
);
