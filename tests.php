<?php

// modify these to fit your pproject

$group_test_name = 'All liquid tests';
$path_to_simpletest = '../simpletest/';

// include library
require_once(dirname(__FILE__).'/liquid.php');

// include test classes
require_once($path_to_simpletest.'unit_tester.php');
require_once($path_to_simpletest.'reporter.php');

// create tests
$test = &new GroupTest($group_test_name);

$path = dirname(__FILE__).'/test/';

// include all classes
$dir = dir($path);

while(($file = $dir->read()) !== false ) {
	if (substr($file, 0, 1) == '.') {
		continue;
	}
	
	if (is_file($path.$file) && substr($file, -9) == '_test.php') {
		$test->addTestFile($path.$file);
	}
	
}

// run!
$test->run(new HtmlReporter());

?>
