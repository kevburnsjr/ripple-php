<?php

namespace Ripple\Test;

// SCREAM REAL LOUD! </peewee>
error_reporting(-1);

require_once('riak-php-client/riak.php');
require_once('../Ripple.class.php');

// Load all suites
$suites_dir = opendir('suites');
while(false !== ($file = readdir($suites_dir))) {
	if (( $file != '.' ) && ( $file != '..' ) && strpos($file,'.')!==false) {
		require_once('suites/'.$file);
	}
}
closedir($suites_dir);

// Load all Models
$classes_dir = opendir('models'); 
while(false !== ( $file = readdir($classes_dir))) {
	if (( $file != '.' ) && ( $file != '..' )) {
		require_once('models/'.$file);
	}
}
closedir($classes_dir);

// Load all Fixtures
$fixtures = new \StdClass();
$fixtures_dir = opendir('fixtures'); 
while(false !== ( $file = readdir($fixtures_dir))) {
	if (( $file != '.' ) && ( $file != '..' )) {
		$path = 'fixtures/'.$file;
		$frags = explode('.',$file);
		if(count($frags) == 3) {
			$class_name = '\Ripple\Test\Model\\'.$frags[1];
			$fixtures->$frags[0] = new $class_name(file_get_contents($path));
		} else {
			$fixtures->$file = json_decode(file_get_contents($path));
		}
	}
}
closedir($fixtures_dir);