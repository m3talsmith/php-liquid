<?php


class LiquidBlockTester extends UnitTestCase {
	
	function test_blackspace() {
		$template = new LiquidTemplate;
		$template->parse('  ');
		$this->assertEqual(array('  '), $template->root->nodelist);
				
	}
	
	function test_variable_beginning() {
		$template = new LiquidTemplate;
		$template->parse('{{funk}}  ');
		
		$this->assertEqual(2, count($template->root->nodelist));
		$this->assertIsA($template->root->nodelist[0], 'LiquidVariable');
		$this->assertIsA($template->root->nodelist[1], 'string');

	}

	function test_variable_end() {
		$template = new LiquidTemplate;
		$template->parse('  {{funk}}');
		
		$this->assertEqual(2, count($template->root->nodelist));
		$this->assertIsA($template->root->nodelist[0], 'string');
		$this->assertIsA($template->root->nodelist[1], 'LiquidVariable');
		

	}

	function test_variable_middle() {
		$template = new LiquidTemplate;
		$template->parse('  {{funk}}  ');
		
		$this->assertEqual(3, count($template->root->nodelist));
		$this->assertIsA($template->root->nodelist[0], 'string');		
		$this->assertIsA($template->root->nodelist[1], 'LiquidVariable');
		$this->assertIsA($template->root->nodelist[2], 'string');

	}	

	function test_variable_many_embedded_fragments() {
		$template  = new LiquidTemplate;
		$template->parse('  {{funk}}  {{soul}}  {{brother}} ');
		
		$this->assertEqual(7, count($template->root->nodelist));
		$this->assertIsA($template->root->nodelist[0], 'string');		
		$this->assertIsA($template->root->nodelist[1], 'LiquidVariable');
		$this->assertIsA($template->root->nodelist[2], 'string');
		$this->assertIsA($template->root->nodelist[3], 'LiquidVariable');
		$this->assertIsA($template->root->nodelist[4], 'string');
		$this->assertIsA($template->root->nodelist[5], 'LiquidVariable');
		$this->assertIsA($template->root->nodelist[6], 'string');
		
	}

	function test_with_block() {
		$template = new LiquidTemplate;
		$template->parse('  {% comment %}  {% endcomment %} ');		
		
		$this->assertEqual(3, count($template->root->nodelist));
		$this->assertIsA($template->root->nodelist[0], 'string');		
		$this->assertIsA($template->root->nodelist[1], 'CommentLiquidTag');
		$this->assertIsA($template->root->nodelist[2], 'string');
	
	}
	
	
}

?>