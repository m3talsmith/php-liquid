<?php

class LiquidVariableTester extends UnitTestCase {
	
	function test_variable() {
		$var = new LiquidVariable('hello');
		$this->assertEqual('hello', $var->name);
		
	}
	
	function test_filters() {
		$var = new LiquidVariable('hello | textileze');
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('textileze', array())), $var->filters);
		
		$var = new LiquidVariable('hello | textileze | paragraph');
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('textileze', array()), array('paragraph', array())), $var->filters);

		$var = new LiquidVariable(" hello | strftime: '%Y'");
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('strftime', array("'%Y'"))), $var->filters);
		
		$var = new LiquidVariable(" 'typo' | link_to: 'Typo', true ");
		$this->assertEqual("'typo'", $var->name);
		$this->assertEqual(array(array('link_to', array("'Typo'", "true"))), $var->filters);
		
		$var = new LiquidVariable(" 'typo' | link_to: 'Typo', false ");
		$this->assertEqual("'typo'", $var->name);
		$this->assertEqual(array(array('link_to', array("'Typo'", "false"))), $var->filters);
		
		$var = new LiquidVariable(" 'foo' | repeat: 3 ");
		$this->assertEqual("'foo'", $var->name);
		$this->assertEqual(array(array('repeat', array("3"))), $var->filters);				
		
		$var = new LiquidVariable(" 'foo' | repeat: 3, 3" );
		$this->assertEqual("'foo'", $var->name);
		$this->assertEqual(array(array('repeat', array("3", "3"))), $var->filters);		
		
		$var = new LiquidVariable(" 'foo' | repeat: 3, 3, 3 ");
		$this->assertEqual("'foo'", $var->name);
		$this->assertEqual(array(array('repeat', array("3", "3", "3"))), $var->filters);				
		
		$var = new LiquidVariable(" hello | strftime: '%Y, okay?'");
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('strftime', array("'%Y, okay?'"))), $var->filters);		
		
		$var = new LiquidVariable(" hello | things: \"%Y, okay?\", 'the other one'");
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('things', array('"%Y, okay?"', "'the other one'"))), $var->filters);
		
	}
	
	function test_filters_without_whitespace() {
		$var = new LiquidVariable('hello | textileze | paragraph');
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('textileze', array()), array('paragraph', array())), $var->filters);		
		
		$var = new LiquidVariable('hello|textileze|paragraph');
		$this->assertEqual('hello', $var->name);
		$this->assertEqual(array(array('textileze', array()), array('paragraph', array())), $var->filters);		
		
	}
	
	function test_symbol() {
		$var = new LiquidVariable("http://disney.com/logo.gif | image: 'med' ");
		$this->assertEqual('http://disney.com/logo.gif', $var->name);
		$this->assertEqual(array(array('image', array("'med'"))), $var->filters);			
		
	}
	
	function test_string_single_quoted() {
		$var = new LiquidVariable(' "hello" ');
		$this->assertEqual('"hello"', $var->name);
		
	}
	
	function test_string_double_quoted() {
		$var = new LiquidVariable(" 'hello' ");
		$this->assertEqual("'hello'", $var->name);		
		
	}
	
	function test_integer() {
		$var = new LiquidVariable(' 1000 ');
		$this->assertEqual('1000', $var->name);
	}
	
	function test_float() {
		$var = new LiquidVariable(' 1000.01 ');
		$this->assertEqual('1000.01', $var->name);
		
	}
	
	function test_string_with_special_chars() {
		$var = new LiquidVariable("'hello! $!@.;\"ddasd\" ' ");
		$this->assertEqual("'hello! $!@.;\"ddasd\" '", $var->name);
	}
	
	function test_string_dot() {
		$var = new LiquidVariable(" test.test ");
		$this->assertEqual('test.test', $var->name);
		
	}
	
	
}

class VariableResolutionTest extends UnitTestCase {
	
	function test_simple_variable() {
		
		$template = new LiquidTemplate();
		$template->parse("{{test}}");
		$this->assertEqual('worked', $template->render(array('test'=>'worked')));
		
		
	}
	
	function test_simple_with_whitespaces() {
		$template = new LiquidTemplate();

	    $template->parse('  {{ test }}  ');
		$this->assertEqual('  worked  ', $template->render(array('test' => 'worked')));
		$this->assertEqual('  worked wonderfully  ', $template->render(array('test' => 'worked wonderfully')));
		
	}
	
	function test_ignore_unknown() {
		$template = new LiquidTemplate();
		
		$template->parse('{{ test }}');
		$this->assertEqual('', $template->render());
		
	}
	
	function test_array_scoping() {
		$template = new LiquidTemplate();
		
		$template->parse('{{ test.test }}');
		$this->assertEqual('worked', $template->render(array('test'=>array('test'=>'worked'))));
		
		// this wasn't working properly in if tests, test seperately
		$template->parse('{{ foo.bar }}');
		$this->dump($template->render(array('foo' => array())));
		
	}
	
}


?>