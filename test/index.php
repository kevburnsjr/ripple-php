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
  <title>ripple-php</title>
  <style>
	h1 { line-height: 1em; font-size: 24px; margin: 0; margin-bottom: 10px; width: 100%; float: left; background: #000; }
	h1 a { color: #fff; text-decoration: none; font-family: arial; font-weight: normal; padding: 10px 10px; float: left; }
	h1 a:hover { text-decoration: underline; }
	a {outline: 0;}
	pre { background: #ddd; padding: 20px; margin: 0;}
	ul.nav { float: left; margin: 0; padding: 0; clear: left; }
	ul.nav li { float: left; list-style: none; }
	ul.nav li a { float: left; line-height: 30px; padding: 0 10px; text-decoration: none; color: #666; font-weight: bold; font-family: arial; }
	ul.nav li a:hover { background: #eee;  color: #000; }
	ul.nav li.active a { background: #ddd; color: #000; }
	ul.nav li.toggle_all_source a { margin-left: 20px; font-size: 11px; }
	.conn-error { background: #a00; color: #fff; padding: 60px 0; font-size: 30px; text-align: center; font-family: sans-serif; }
	.source { display: none; background: #fff; padding: 0; border: 2px solid #ccc; margin-bottom: 1em; }
	.toggle_source { display: none; background: #fff; padding: 0; border: 2px solid #ccc; }
	.pass { background: #080; color: #fff; padding: 0 4px 0 3px; }
	.fail { background: #a00; color: #fff; padding: 0 4px 0 3px; }
	.choose { font-size: 16px; padding: 40px; color: #666; }
	.choose ul li { padding: 5px 0; list-style: none; }
  </style>
</head>
<h1><a href="?">ripple-php</a></h1>
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
	echo "<pre class='choose'>ripple-php is an ODM for Riak inspired by Ripple.\n";
	echo "<ul>";
	foreach($testsets as $func => $title) {
		$q = http_build_query(array('test' => $func));
		echo "<li>Test <a href='?{$q}'>".ucwords($func)."</a></li>";
	}
	echo "</ul></pre>";
	
} else {
	$test_name = $_GET['test'];
	$total = $total_passed = 0;
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
					$total_passed++;
					echo " <span class='pass'>Passed</span> ";
				} else {
					echo " <span class='fail'>Failed</span> ";
					print_r($error);
				}
				$total++;
				$time = round((microtime(true) - $func_timer_start)*1000, 3);
				echo "($time ms) \n\n";
				$d = $show_source ? 'block' : 'none';
				echo "<div class='source' style='display: {$d}'id='{$id}'>\n{$code}\n\n</div>";
			}
		}
		$timer_end = microtime(true);
		echo "- Total Time: " . round(($timer_end - $timer_start)*1000, 3)." ms\n";
		echo "- Total Passed: {$total_passed}/{$total}\n";
		echo "</pre>";
	} else {
		echo "<pre class='conn-error'>Could not find test `".htmlentities($test_name)."`\n";
		echo "Chooose a test from the list above.</pre>";
	}
}

?>
<script>
function showSource(id) {
	var el = document.getElementById(id);
	el.style.display = el.style.display == 'none' ? 'block' : 'none';
}
</script>
</body>
</html>