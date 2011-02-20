<?php

$testsets['client'] = array(

	"Get Client" => function($fxs) {
		$client = \Ripple::client();
		return $client instanceOf \RiakClient;
	}
	
);
