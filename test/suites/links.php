<?php

namespace Ripple\Test;

$testsets['links'] = array(

	"Embed Document" => function($fxs) {
		$client = new Client('dave_embedded_address', $fxs->client1);
		$address = new Address(null, $fxs->address1);
		$client->address = $address;
		return $client->save();
	},

	"Link to Document" => function($fxs) {
		$client = new Client('dave_link_to_address', $fxs->client1);
		$address = new Address('dave_linked_to_address', $fxs->address1);
		$client->addLink($address, 'address');
		return $client->save();
	},
	
);
