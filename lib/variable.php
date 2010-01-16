<?php
/**
 * Liquid for PHP
 * 
 * @package Liquid
 * @copyright Copyright (c) 2006 Mateo Murphy, 
 * based on Liquid for Ruby (c) 2006 Tobias Luetke
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 */

/**
 * Implements a template variable
 *
 * @package Liquid
 */
class LiquidVariable {
	
	/**
	 * The filters to execute on the variable
	 *
	 * @var array
	 */
	var $filters;
	
	/**
	 * The name of the variable
	 *
	 * @var string
	 */
	var $name;
	
	/**
	 * The markup of the variable
	 *
	 * @var string
	 */
	var $markup;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @return LiquidVariable
	 */
	function LiquidVariable($markup) {
		$this->markup = $markup;
		
		$quoted_fragment_regexp = new LiquidRegexp('/\s*('.LIQUID_QUOTED_FRAGMENT.')/');
		$filter_seperator_regexp = new LiquidRegexp('/'.LIQUID_FILTER_SEPERATOR.'\s*(.*)/');
		$filter_split_regexp = new LiquidRegexp('/'.LIQUID_FILTER_SEPERATOR.'/');
		$filter_name_regexp = new LiquidRegexp('/\s*(\w+)/');
		$filter_argument_regexp = new LiquidRegexp('/(?:'.LIQUID_FILTER_ARGUMENT_SEPERATOR.'|'.LIQUID_ARGUMENT_SPERATOR.')\s*('.LIQUID_QUOTED_FRAGMENT.')/');
		
		$quoted_fragment_regexp->match($markup);
		$this->name = $quoted_fragment_regexp->matches[1];
		
		if ($filter_seperator_regexp->match($markup)) {
			
			$filters = $filter_split_regexp->split($filter_seperator_regexp->matches[1]);
			
			foreach($filters as $filter) {
				$filter_name_regexp->match($filter);
				$filtername = $filter_name_regexp->matches[1];
				
				$filter_argument_regexp->match_all($filter);
				$matches = array_flatten($filter_argument_regexp->matches[1]);
				
				$this->filters[] = array($filtername, $matches);
				
			}
			
		} else {
			$this->filters = array();
			
		}
		
	}
	
	/**
	 * Renders the variable with the data in the context
	 *
	 * @param LiquidContext $context
	 */
	
	function render($context) {
		$output = $context->get($this->name);
		//debug('name', $this->name, 'output', $output);
		foreach ($this->filters as $filter) {
			list($filtername, $filter_arg_keys) = $filter;
			
			$filter_arg_values = array();
			
			foreach($filter_arg_keys as $arg_key) {
				$filter_arg_values[] = $context->get($arg_key);
				
			}
			
			$output = $context->invoke($filtername, $output, $filter_arg_values);
			
		}
		
		return $output;
		
	}
	
	
}



?>