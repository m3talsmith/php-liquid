<?php

class ParsingQuirksTester extends UnitTestCase {
	
	
	function test_error_with_css() {
		$text = " div { font-weight: bold; } ";
		$template = new LiquidTemplate();
		$template->parse($text);
		
		$this->assertEqual($text, $template->render());
		$this->assertIsA($template->root->nodelist[0], 'string');
	}
	
}



?>