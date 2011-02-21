<?php

require_once('config.php');

ksort($testsets);

if(isset($_GET['show_source'])) {
	$show_source = $_GET['show_source'];
	setcookie('show_source', $show_source);
} else {
	$show_source = isset($_COOKIE['show_source']) ? $_COOKIE['show_source'] : false;
}
$toggle_source = http_build_query(array_merge($_GET, array('show_source' => !$show_source)));

?><!doctype html>
<head>
  <title>Ripple Test</title>
</head>
<h1><a href="?">Ripple Test</a></h1>
<ul class="nav">
	<? foreach($testsets as $func => $title) {
		$q = http_build_query(array('test' => $func));
		$attr_act = isset($_GET['test']) && $func == $_GET['test'] ? ' class="active"' : null;
	?>
	<li<?=$attr_act?>><a href="?<?=$q?>"><?=ucwords($func)?></a></li>
	<? } ?>
	<li class="toggle_all_source"><a href="?<?=$toggle_source?>">Toggle source</a></li>
</ul>
<div style="clear: both;"></div>
<?
if(!\Ripple::client()->isAlive()) {
	echo "<div class='conn-error'>Connection failed on 127.0.0.1:8098</div>";
} else if(!isset($_GET['test'])) {
	echo "<pre>Chooose a test set from the list above.</pre>";
} else {
	$test_name = $_GET['test'];
	if(isset($testsets[$test_name])) {
		$testset = $testsets[$test_name];
		echo "<pre>----------- -= ".ucwords($test_name)." =- ------------\n\n";
		$timer_start = microtime(true);
		foreach($testset as $name => $test) {
			if($test instanceof \Closure) {
				$code = \Ripple\Test\fetchCode($test);
				$id = md5($name);
				echo "<a href='javascript:showSource(\"{$id}\")'>{$name}</a> ...";
				$func_timer_start = microtime(true);
				$error = null;
				try {
					$passed = $test($fixtures);
				} catch (Exception $e) {
					$passed = false;
					$error = $e;
				}
				if($passed) {
					echo " Passed. ";
				} else {
					echo " FAILED\n";
					print_r($error);
				}
				$time = round((microtime(true) - $func_timer_start)*1000, 3);
				echo "($time ms) \n\n";
				$d = $show_source ? 'block' : 'none';
				echo "<div class='source' style='display: {$d}'id='{$id}'>\n{$code}\n\n</div>";
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

?>
<style>
h1 { margin-top: 0; }
a {outline: 0;}
pre { background: #ddd; padding: 20px; margin: 0;}
ul.nav { float: left; margin: 0; padding: 0;}
ul.nav li { float: left; list-style: none; }
ul.nav li a { float: left; line-height: 30px; padding: 0 10px; text-decoration: none; color: #666; font-weight: bold; font-family: arial; }
ul.nav li a:hover { background: #eee;  color: #000; }
ul.nav li.active a { background: #ddd; color: #000; }
ul.nav li.toggle_all_source a { margin-left: 20px; font-size: 11px; }
.conn-error { background: #a00; color: #fff; padding: 60px 0; font-size: 30px; text-align: center; font-family: sans-serif; }
.source { display: none; background: #fff; padding: 0; border: 2px solid #ccc; margin-bottom: 1em; }
.toggle_source { display: none; background: #fff; padding: 0; border: 2px solid #ccc; }
</style>
<script>
function showSource(id) {
	var el = document.getElementById(id);
	el.style.display = el.style.display == 'none' ? 'block' : 'none';
}
</script>
</body>
</html>