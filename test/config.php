<?php

namespace Ripple\Test;

// SCREAM REAL LOUD! </peewee>
error_reporting(-1);

require_once('riak-php-client/riak.php');
require_once('../Ripple.class.php');

$source = array();

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

function fetchCode($closure) {
	$reflection = new \ReflectionFunction($closure);
	
	// Open file and seek to the first line of the closure
	$file = new \SplFileObject($reflection->getFileName());
	$file->seek($reflection->getStartLine()-1);

	// Retrieve all of the lines that contain code for the closure
	$lines = array();
	while ($file->key() < $reflection->getEndLine()) {
		$lines[] = $file->current();
		$file->next();
	}
	
	$code = "<?php\n".implode('',array_slice($lines,1,-1));
	$html = highlight_string($code, 1);
	$html = str_replace('<code>','',$html);
	$html = str_replace('</code>','',$html);
	$html = str_replace("\n",'',$html);
	$html = str_replace('&lt;?php','',$html);
	$html = preg_replace('/(\t\t)/','',$html);
	return $html;
}