<?php 

class MoneyFilter {
	
	function money($value) {
		return sprintf(' %d$ ', $value);
		
	}
	
	function money_with_underscore($value) {
		return sprintf(' %d$ ', $value);
		
	}
	
}

class CanadianMoneyFilter {
	
	function money($value) {
		return sprintf(' %d$ CAD ', $value);
		
	}
	
}


class LiquidFiltersTester extends UnitTestCase {
	
	/**
	 * The current context
	 *
	 * @var LiquidContext
	 */
	var $context;
	
	function setup() {
		$this->context = new LiquidContext();
		
	}
	
	function test_local_filter() {
		$var = new LiquidVariable('var | money');
		$this->context->set('var', 1000);
		$this->context->add_filters(new MoneyFilter());
		$this->assertIdentical(' 1000$ ', $var->render($this->context));
		
	}
	
	function test_underscore_in_filter_name() {
		$var = new LiquidVariable('var | money_with_underscore ');
		$this->context->set('var', 1000);
		$this->context->add_filters(new MoneyFilter());
		$this->assertIdentical(' 1000$ ', $var->render($this->context));		
		
	}

	function test_second_filter_overwrites_first() {
		$var = new LiquidVariable('var | money ');
		$this->context->set('var', 1000);
		$this->context->add_filters(new MoneyFilter(), 'money');
		$this->context->add_filters(new CanadianMoneyFilter(), 'money');
		$this->assertIdentical(' 1000$ CAD ', $var->render($this->context));		
		
	}
	
	function test_size() {
		$var = new LiquidVariable("var | size");
		$this->context->set('var', 1000);
		//context.add_filters(MoneyFilter)
		$this->assertEqual(4, $var->render($this->context));		
	}
	
	function test_join() {
		$var = new LiquidVariable("var | join");
	
		$this->context->set('var', array(1, 2, 3, 4));
		$this->assertEqual("1 2 3 4", $var->render($this->context));		
		
	}
	
	function test_strip_html() {
		$var = new LiquidVariable("var | strip_html");
		
 	    $this->context->set('var', "<b>bla blub</a>");
 	    $this->assertEqual("bla blub", $var->render($this->context));  
	}
	
	
}


class LiquidFiltersInTemplate extends UnitTestCase {
	
	function test_local_global() {
		$template = new LiquidTemplate;
		$template->register_filter(new MoneyFilter());
		
		$template->parse('{{1000 | money}}');
		$this->assertIdentical(' 1000$ ', $template->render());	
		$this->assertIdentical(' 1000$ CAD ', $template->render(null, new CanadianMoneyFilter()));	
	}
	
}

?>