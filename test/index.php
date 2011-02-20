<?php

require_once('config.php');

?>
<h1><a href="?">Ripple Test</a></h1>
<ul class="nav">
	<? foreach($testsets as $func => $title) { 
		$attr_act = isset($_GET['test']) && $func == $_GET['test'] ? ' class="active"' : null;
	?>
	<li<?=$attr_act?>><a href="?test=<?=$func?>"><?=ucfirst($func)?></a></li>
	<? } ?>
</ul>
<div style="clear: both;"></div>
<style>
a {outline: 0;}
pre { background: #ddd; padding: 20px; margin: 0;}
ul.nav { float: left; margin: 0; padding: 0;}
ul.nav li { float: left; list-style: none; }
ul.nav li a { float: left; line-height: 30px; padding: 0 10px; text-decoration: none; color: #666; font-weight: bold; font-family: arial; }
ul.nav li a:hover { background: #eee;  color: #000; }
ul.nav li.active a { background: #ddd; color: #000; }
</style>
<?
if(!isset($_GET['test'])) {
	echo "<pre>Chooose a test set from the list above.</pre>";
} else {
	$test_name = $_GET['test'];
	if(isset($testsets[$test_name])) {
		$testset = $testsets[$test_name];
		echo "<pre>----------- -= ".ucfirst($test_name)." =- ------------\n\n";
		$timer_start = microtime(true);
		foreach($testset as $name => $test) {
			if($test instanceof \Closure) {
				$func_timer_start = microtime(true);
				echo $name." ...";
				$error = null;
				try {
					$passed = $test($fixtures);
				} catch (Exception $e) {
					$passed = false;
					$error = $e;
				}
				if($passed) {
					echo " Passed. \n";
				} else {
					echo " FAILED\n";
					print_r($error);
				}
				$time = round((microtime(true) - $func_timer_start)*1000, 3);
				echo "($time ms) \n\n";
			}
		}
		$timer_end = microtime(true);
		echo "- Total: " . round(($timer_end - $timer_start)*1000, 3);
		echo " ms\n\n<pre>";
	} else {
		echo "<pre>Could not find test $test_name\n";
		echo "Chooose a test from the list above.</pre>";
	}
}
