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
 * The template class.
 * 
 * @example 
 * $tpl = new LiquidTemplate();
 * $tpl->parse(template_source);
 * $tpl->render(array('foo'=>1, 'bar'=>2);
 *
 * @package Liquid
 */
class LiquidTemplate {
	
	/**
	 * The root of the node tree
	 *
	 * @var LiquidDocument
	 */
	var $root;
	
	/**
	 * The file system to use for includes
	 *
	 * @var LiquidBlankFileSystem
	 */
	var $file_system;
	
	/**
	 * Globally included filters
	 *
	 * @var array
	 */
	var $filters;
	
	/**
	 * Constructor
	 *
	 * @return LiquidTemplate
	 */
	function LiquidTemplate() {
		$this->file_system = new LiquidBlankFileSystem();
		$this->filters = array();
	}
/*	this is currently not needed
	function register_tag($name) {
		$this->tags[$name] = $name;
		
	}
*/	
	/**
	 * Register the filter
	 *
	 * @param unknown_type $filter
	 */
	function register_filter($filter) {
		$this->filters[] = $filter;
		
	}	
	
	/**
	 * Tokenizes the given source string
	 *
	 * @param string $source
	 * @return array
	 */
	function tokenize($source) {
		if (!$source) {
			return array();
			
		}
		
		$tokens = preg_split(LIQUID_TOKENIZATION_REGEXP, $source, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		return $tokens;
		
	}
	
	/**
	 * Parses the given source string
	 *
	 * @param string $source
	 */
	function parse($source) {
		$this->root = new LiquidDocument(LiquidTemplate::tokenize($source), $this->file_system);	
		
	}
	
	/**
	 * Renders the current template
	 *
	 * @param array $assigns An array of values for the template
	 * @param array $filters Additional filters for the template
	 * @param array $registers Additional registers for the template
	 * @return string
	 */
	function render($assigns = null, $filters = null, $registers = null) {
		if (is_null($assigns)) {
			$assigns = array();
		}

		$context = new LiquidContext($assigns, $registers);
		
		if (!is_null($filters)) {
			if (is_array($filters)) {
				array_merge($this->filters, $filters);
					
			} else {
				$this->filters[] = $filters;
				
			}
		}
	
		foreach ($this->filters as $filter) {
			$context->add_filters($filter);
			
		}
		
		return $this->root->render($context);
		
	}
	
}

?>