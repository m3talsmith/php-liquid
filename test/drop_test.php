<?php

class ContextDrop extends LiquidDrop {
	function before_method($method) {
		return $this->context->get($method);
		
	}
	
}

class TextDrop extends LiquidDrop {
	function get_array() {
		return array('text1', 'text2');
		
	}

	function text() {
		return 'text1';
		
	}
}

class CatchallDrop extends LiquidDrop {
	function before_method($method) {
		return 'method: '.$method;
		
	}
	
}

class ProductDrop extends LiquidDrop {
	function top_sales() {
		trigger_error('worked', E_USER_ERROR);
		
	}
	
	function texts() {
		return new TextDrop();
		
	}
	
	function catchall() {
		return new CatchallDrop();
		
	}
	
	function context() {
		return new ContextDrop();
	}
	
	function callmenot() {
		return "protected";
		
	}
	
}

class LiquidDropTester extends UnitTestCase {
	
	function test_product_drop() {
		
		$template = new LiquidTemplate;
		$template->parse('  ');
		$template->render(array('product' => new ProductDrop));
		$this->assertNoErrors();
		
		
	    $template = new LiquidTemplate;
		$template->parse( ' {{ product.top_sales }} '  );
	    $template->render(array('product' => new ProductDrop));
	    $this->assertError('worked');

	}

	function test_text_drop() {
		
		$template = new LiquidTemplate;
		$template->parse(' {{ product.texts.text }} ');
		$output = $template->render(array('product' => new ProductDrop()));	
		$this->assertEqual(' text1 ', $output);

		$template = new LiquidTemplate;
		$template->parse(' {{ product.catchall.unknown }} ');
		$output = $template->render(array('product' => new ProductDrop()));	
		$this->assertEqual(' method: unknown ', $output);		
		
	}
	
	// needed to rename call to array because array is a reserved word in php
	
	function test_text_array_drop() {
		$template = new LiquidTemplate;
		$template->parse('{% for text in product.texts.get_array %} {{text}} {% endfor %}');
		$output = $template->render(array('product' => new ProductDrop()));
		
		$this->assertEqual(' text1  text2 ', $output);
		
	}
	
	
	function test_context_drop() {
		$template = new LiquidTemplate;
		$template->parse(' {{ context.bar }} ');
		$output = $template->render(array('context' => new ContextDrop(), 'bar'=>'carrot'));	
		$this->assertEqual(' carrot ', $output);		
		
	}
	
	function test_nested_context_drop() {
		$template = new LiquidTemplate;
		$template->parse(' {{ product.context.foo }} ');
		$output = $template->render(array('product' => new ProductDrop(), 'foo'=>'monkey'));	
		$this->assertEqual(' monkey ', $output);		

	}
	
	// skip this test as php4 doesn't support protected vars
	/*
	function test_protected() {
		$template = new LiquidTemplate;
		$template->parse(' {{ product.callmenot }} ');
		$output = $template->render(array('product' => new ProductDrop()));	
		$this->assertEqual('  ', $output);			
		
	}
	
	*/
	
}

?>