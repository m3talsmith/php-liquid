<?php

require_once('test_helper.php');


class FileSystemTester extends LiquidTestCase {
	
	function test_default() {
		$file_system = new LiquidBlankFileSystem();
		$file_system->read_template_file('dummy');
		$this->assertError("This liquid context does not allow includes.");
		
	}
	
	function test_local() {
		
		$root = dirname(__FILE__).'/templates/';
		
		$file_system = new LiquidLocalFileSystem($root);
		$this->assertEqual($root."_mypartial.liquid", $file_system->full_path("mypartial"));
		$this->assertEqual($root."dir/_mypartial.liquid", $file_system->full_path("dir/mypartial"));

		
		$root = dirname(__FILE__).'/dir/templates/';
		$file_system->full_path('../dir/mypartial');
		$this->assertError("Illegal template name '../dir/mypartial'");
		
		$file_system->full_path("/dir/../../dir/mypartial");
		$this->assertError();
		
		$file_system->full_path("/etc/passwd");
		$this->assertError();
	}
}



?>