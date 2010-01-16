<?php

class LiquidTemplateTester extends UnitTestCase {

	function test_tokenize_strings() {
 	    $this->assertEqual(array(' '), LiquidTemplate::tokenize(' '));
 	    $this->assertEqual(array('hello world'), LiquidTemplate::tokenize('hello world'));
	}
	
	function test_tokenize_variables() {
		$this->assertEqual(array('{{funk}}'), LiquidTemplate::tokenize('{{funk}}'));
		$this->assertEqual(array(' ', '{{funk}}', ' '), LiquidTemplate::tokenize(' {{funk}} '));
		$this->assertEqual(array(' ', '{{funk}}', ' ', '{{so}}', ' ', '{{brother}}', ' '), LiquidTemplate::tokenize(' {{funk}} {{so}} {{brother}} '));
		$this->assertEqual(array(' ', '{{  funk  }}', ' '), LiquidTemplate::tokenize(' {{  funk  }} '));
		
		
	}

	function test_tokenize_blocks() {
		$this->assertEqual(array('{%comment%}'), LiquidTemplate::tokenize('{%comment%}'));
		$this->assertEqual(array(' ', '{%comment%}', ' '), LiquidTemplate::tokenize(' {%comment%} '));
		$this->assertEqual(array(' ', '{%comment%}', ' ', '{%endcomment%}', ' '), LiquidTemplate::tokenize(' {%comment%} {%endcomment%} '));
		$this->assertEqual(array('  ', '{% comment %}', ' ', '{% endcomment %}', ' '), LiquidTemplate::tokenize("  {% comment %} {% endcomment %} "));
	}

	
}

?>