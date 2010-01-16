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

// A selection of standard tags

/**
 * Performs an assignment of one variable to another
 * 
 * @example 
 * {%assign var = var %}
 *
 * @package Liquid
 */
class AssignLiquidTag extends LiquidTag {

	/**
	 * The variable to assign from
	 *
	 * @var string
	 */
	var $from;
	
	/**
	 * The variable to assign to
	 *
	 * @var string
	 */
	var $to;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return AssignLiquidTag
	 */
	function AssignLiquidTag($markup, & $tokens, & $file_system) {
		$syntax_regexp = new LiquidRegexp('/(\w+)\s*=\s*('.LIQUID_ALLOWED_VARIABLE_CHARS.'+)/');
		
		if ($syntax_regexp->match($markup)) {
			$this->to = $syntax_regexp->matches[1];
			$this->from = $syntax_regexp->matches[2];
			
		} else {
			trigger_error("Syntax Error in 'assign' - Valid syntax: assign [var] = [source]");
			
		}

	}

	/**
	 * Renders the tag
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context) {
		$context->set($this->to, $context->get($this->from));
		
	}
	
}

/**
 * Captures the output inside a block and assigns it to a variable
 * 
 * @example
 * {% capture foo %} bar {% endcapture %}
 *
 * @package Liquid
 */
class CaptureLiquidTag extends LiquidBlock {
	
	/**
	 * The variable to assign to
	 *
	 * @var string
	 */
	var $to;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param Array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return CaptureLiquidTag
	 */
	function CaptureLiquidTag($markup, & $tokens, & $file_system) {
		$syntax_regexp = new LiquidRegexp('/(\w+)/');
		
		if ($syntax_regexp->match($markup)) {
			$this->to = $syntax_regexp->matches[1];
			parent::LiquidTag($markup, $tokens, $file_system);
			
		} else {
			trigger_error("Syntax Error in 'capture' - Valid syntax: assign [var] = [source]");
			
		}

	}

	/**
	 * Renders the block
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context) {
		$output = parent::render($context);
		
		$context->set($this->to, $output);
		
	}	
	
}

/**
 * Creates a comment; everything inside will be ignored
 *
 * @example
 * {% comment %} This will be ignored {% endcomment %}
 * 
 * @package Liquid
 */
class CommentLiquidTag extends LiquidBlock {
	
	/**
	 * Renders the block
	 *
	 * @param LiquidContext $context
	 * @return string
	 */
	function render(& $context) {
		return '';
		
	}
	
}

/**
 * Cycles between a list of values; calls to the tag will return each value in turn
 * 
 * @example
 * {%cycle "one", "two"%} {%cycle "one", "two"%} {%cycle "one", "two"%}
 * 
 * this will return:
 * one two one
 * 
 * Cycles can also be named, to differentiate between multiple cycle with the same values:
 * {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %} {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %}
 * 
 * will return
 * one one two two
 * 
 * @package Liquid
 */
class CycleLiquidTag extends LiquidTag {
	
	/**
	 * The name of the cycle; if none is given one is created using the value list
	 *
	 * @var string
	 */
	var $name;

	/**
	 * The variables to cycle between
	 *
	 * @var array
	 */
	var $variables;	
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @return CycleLiquidTag
	 */
	function CycleLiquidTag($markup, & $tokens, & $file_system) {
		$simple_syntax = new LiquidRegexp("/".LIQUID_QUOTED_FRAGMENT."/");
		$named_syntax = new LiquidRegexp("/(".LIQUID_QUOTED_FRAGMENT.")\s*\:\s*(.*)/");
		
		if ($named_syntax->match($markup)) {
			$this->variables = $this->variables_from_string($named_syntax->matches[2]);
			$this->name = $named_syntax->matches[1];
			
		} elseif ($simple_syntax->match($markup)) {
			$this->variables = $this->variables_from_string($markup);
			$this->name = "'".implode($this->variables)."'";
			
		} else {
			trigger_error("Syntax Error in 'cycle' - Valid syntax: cycle [name :] var [, var2, var3 ...]");
			
		}
		

	}

	/**
	 * Renders the tag
	 * 
	 * @var LiquidContext $context
	 * @return string
	 */
	function render(& $context) {
		
		$context->push();
		
		$key = $context->get($this->name);
		
		if (isset($context->registers['cycle'][$key])) {
			$iteration = $context->registers['cycle'][$key];
		} else {
			$iteration = 0;
		}
		
		$result = $context->get($this->variables[$iteration]);
		
		$iteration += 1;

		if ($iteration >= count($this->variables)) {
			$iteration = 0;
		}
		
		$context->registers['cycle'][$key] = $iteration;
		
		$context->pop();
		
		return $result;
	}
	
	/**
	 * Extract variables from a string of markup
	 * 
	 * @param string $markup
	 * @return array;
	 */
	function variables_from_string($markup) {
		$regexp = new LiquidRegexp('/\s*('.LIQUID_QUOTED_FRAGMENT.')\s*/');
		$parts = explode(',', $markup);
		$result = array();
		
		foreach($parts as $part) {
			$regexp->match($part);
			
			if ($regexp->matches[1]) {
				$result[] = $regexp->matches[1];
			}
			
		}
		
		return $result;
		
	}
	
}

/**
 * Loops over an array, assigning the current value to a given variable
 * 
 * @example
 * {%for item in array%} {{item}} {%endfor%}
 * 
 * With an array of 1, 2, 3, 4, will return 1 2 3 4
 * 
 * @package Liquid
 */
class ForLiquidTag extends LiquidBlock {
	
	/**
	 * The collection to loop over
	 *
	 * @var array
	 */
	var $collection_name;
	
	/**
	 * The variable name to assign collection elemnts to 
	 *
	 * @var string
	 */
	var $variable_name;

	/**
	 * The name of the loop, which is a compound of the collection and variable names
	 *
	 * @var string
	 */
	var $name;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return ForLiquidTag
	 */
	function ForLiquidTag($markup, & $tokens, & $file_system) {
		parent::LiquidTag($markup, $tokens, $file_system);
		
		$syntax_regexp = new LiquidRegexp('/(\w+)\s+in\s+('.LIQUID_ALLOWED_VARIABLE_CHARS.'+)/');
		
		if ($syntax_regexp->match($markup, $matches)) {
			$this->variable_name = $syntax_regexp->matches[1];
			$this->collection_name = $syntax_regexp->matches[2];
			$this->name = $syntax_regexp->matches[1].'-'.$syntax_regexp->matches[2];

			$this->extract_attributes($markup);

		} else {
			trigger_error("Syntax Error in 'for loop' - Valid syntax: for [item] in [collection]", E_USER_ERROR);
			
		}
		
	}

	
	/**
	 * Renders the tag
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context) {
		if (!isset($context->registers['for'])) {
			$context->registers['for'] = array();
			
		}
		
		$collection = $context->get($this->collection_name);
		
		if (is_null($collection) || count($collection) == 0) {
			return '';
		}
		
		$range = array(0, count($collection));
		
		if (isset($this->attributes['limit']) || isset($this->attributes['offset'])) {
			
			$offset = 0;
			
			if (isset($this->attributes['offset'])) {
				if ($this->attributes['offset'] == 'continue') {
					$offset = $context->registers['for'][$this->name];
				} else {
					$offset = $context->get($this->attributes['offset']);
				}
			} 
			
			$limit = $context->get($this->attributes['limit']);
			
			$range_end = $limit ? $limit : count($collection) - $offset;
			
			$range = array($offset, $range_end);
			
			$context->registers['for'][$this->name] = $range_end + $offset;
			
		}
		
		$result = '';
		
		$segment = array_slice($collection, $range[0], $range[1]);
		
		if (!count($segment)) {
			return null;
		}
		
		$context->push();
		
		$length = count($segment);

		foreach($segment as $index => $item) {
			$context->set($this->variable_name, $item);
			$context->set('forloop', array(
				'name'		=> $this->name,
				'length' 	=> $length,
				'index' 	=> $index + 1,
				'index0' 	=> $index,
				'rindex'	=> $length - $index,
				'rindex0'	=> $length - $index - 1,
				'first'		=> (int)($index == 0),
				'last'		=> (int)($index == $length - 1)
			
			));
			
			$result .= $this->render_all($this->nodelist, $context);
			
		}
		
		$context->pop();
		
		return $result;
		
	}
	
}

/**
 * Base class for blocks that make logical decisions
 *
 * @package Liquid
 */
class LiquidDecisionBlock extends LiquidBlock {

	/**
	 * The current left variable to compare
	 *
	 * @var string
	 */
	var $left;	
	
	/**
	 * The current right variable to compare
	 *
	 * @var string
	 */
	var $right;
	
	
	/**
	 * Returns a string value of an array for comparisons
	 *
	 * @param mixed $value
	 * @return string
	 */
	function string_value($value) {
		// objects should have a to_string a value to compare to
		if (is_object($value)) {
			if (method_exists($value, 'to_string')) {
				$value = $value->to_string();
			} else {
				trigger_error("Cannot convert $value to string", E_USER_WARNING);
				return false;
			}
			
		}		

		// arrays simply return true
		if (is_array($value)) {
			return true;
			
		}
		
		return $value;
	}
	
	/**
	 * Check to see if to variables are equal in a given context
	 *
	 * @param string $left
	 * @param string $right
	 * @param LiquidContext $context
	 * @return bool
	 */
	function equal_variables($left, $right, & $context) {
		$left = $this->string_value($context->get($left));
		$right = $this->string_value($context->get($right));

		return ($left == $right);	
		
	}
	
	/**
	 * Interpret a comparison 
	 *
	 * @param string $left
	 * @param string $right
	 * @param string $op
	 * @param LiquidContext $context
	 * @return bool
	 */
	function interpret_condition($left, $right, $op = null, & $context) {
		
		if (is_null($op)) {
			$value = $this->string_value($context->get($left));
			return $value;
			
		}

		// values of 'empty' have a special meaning in array comparisons
		if ($right == 'empty' && is_array($context->get($left))) {
			$left = count($context->get($left));
			$right = 0;
			
		} elseif ($left == 'empty' && is_array($context->get($right))) {
			$right = count($context->get($right));
			$left = 0;
			
		} else {
			$left = $context->get($left);
			$right = $context->get($right);

			$left = $this->string_value($left);
			$right = $this->string_value($right);
		}
		
		// special rules for null values
		if (is_null($left) || is_null($right)) {
			// null == null returns true
			if ($op == '==') {
				return true;
			}
			
			// null != anything other than null return true
			if ($op == '!=' && (!is_null($left) || !is_null($right))) {
				return true;
			}
			
			// everything else, return false;
			return false;
		}
		
		// regular rules
		switch ($op) {
			case '==':
				return ($left == $right);
			
			case '!=':
				return ($left != $right);
				
			case '>':
				return ($left > $right);

			case '<':
				return ($left < $right);

			case '>=':
				return ($left >= $right);

			case '<=':
				return ($left <= $right);

			default:
				trigger_error("Error in tag '".$this->name()."' - Unknown operator $op");
				return null;
							
		}
		
	}
	
}

/**
 * An if statement
 * 
 * @example
 * {% if true %} YES {% else %} NO {% endif %}
 * 
 * will return:
 * YES
 *
 * @package Liquid
 */
class IfLiquidTag extends LiquidDecisionBlock {
	
	/**
	 * Nodes to render when condition is true
	 *
	 * @var array
	 */
	var $nodelist_true;
	
	/**
	 * Nodes to render when condition is false
	 *
	 * @var array
	 */
	var $nodelist_false;
	
	/**
	 * Operator for comparison
	 *
	 * @var string
	 */
	var $operator;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return IfLiquidTag
	 */
	function IfLiquidTag($markup, & $tokens, & $file_system) {
		$regex = new LiquidRegexp('/('.LIQUID_QUOTED_FRAGMENT.')\s*([=!<>]+)?\s*('.LIQUID_QUOTED_FRAGMENT.')?/');
		
		$this->nodelist_true = & $this->nodelist;
		$this->nodelist = array();
		
		$this->nodelist_false = array();
		
		parent::LiquidTag($markup, $tokens, $file_system);
		
		if ($regex->match($markup)) {
			$this->left = $regex->matches[1];
			$this->operator = $regex->matches[2];
			$this->right = $regex->matches[3];
			
		} else {
			trigger_error("Syntax Error in tag 'if' - Valid syntax: if [condition]", E_USER_ERROR);
			
		}
		
	}
	
	/**
	 * Handler for unknown tags, handle else tags
	 *
	 * @param string $tag
	 * @param array $params
	 * @param array $tokens
	 */
	function unknown_tag($tag, $params, $tokens) {
		if ($tag == 'else') {
			$this->nodelist = & $this->nodelist_false;
			$this->nodelist_false = array();
		} else {
			parent::unknown_tag($tag, $params, $tokens);
			
		}
		
	}
	
	/**
	 * Render the tag
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context) {
		$context->push();
		
		if ($this->interpret_condition($this->left, $this->right, $this->operator, $context)) {
			$result = $this->render_all($this->nodelist_true, $context);
		} else {
			$result = $this->render_all($this->nodelist_false, $context);
			
		}
		
		$context->pop();
		
		return $result;
	}
	
}

/**
 * A switch statememt
 * 
 * @example
 * {% case condition %}{% when foo %} foo {% else %} bar {% endcase %}
 *
 * @package Liquid
 */
class CaseLiquidTag extends LiquidDecisionBlock {

	/**
	 * Stack of nodelists
	 *
	 * @var array
	 */
	var $nodelists;
	
	/**
	 * The nodelist for the else (default) nodelist
	 *
	 * @var array
	 */
	var $else_nodelist;
	
	/**
	 * The left value to compare
	 *
	 * @var string
	 */
	var $left;
	
	/**
	 * The current right value to compare
	 *
	 * @var unknown_type
	 */
	var $right;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return CaseLiquidTag
	 */
	function CaseLiquidTag($markup, & $tokens, & $file_system) {
		$this->nodelists = array();
		$this->else_nodelist = array();
		
		parent::LiquidTag($markup, $tokens, $file_system);
		
		$syntax_regexp = new LiquidRegexp('/'.LIQUID_QUOTED_FRAGMENT.'/');
		
		if ($syntax_regexp->match($markup, $matches)) {
			$this->left = $syntax_regexp->matches[0];
			
		} else {
			trigger_error("Syntax Error in tag 'case' - Valid syntax: case [condition]", E_USER_ERROR);
			
		}

		
	}
	
	/**
	 * Pushes the last nodelist onto the stack
	 *
	 */
	function end_tag() {
		$this->push_nodelist();
		
	}
	
	/**
	 * Unknown tag handler
	 *
	 * @param string $tag
	 * @param array $params
	 * @param array $tokens
	 */
	function unknown_tag($tag, $params, & $tokens) {
		$when_syntax_regexp = new LiquidRegexp('/'.LIQUID_QUOTED_FRAGMENT.'/');
		
		switch ($tag) {
		case 'when':
			// push the current nodelist onto the stack and prepare for a new one
			if ($when_syntax_regexp->match($params)) {
				$this->push_nodelist();
				$this->right = $when_syntax_regexp->matches[0];
				$this->nodelist = array();
				
			} else {
				trigger_error("Syntax Error in tag 'case' - Valid when condition: when [condition]", E_USER_ERROR);
				
			}
			break;
			
		case 'else':
			// push the last nodelist onto the stack and prepare to recieve the else nodes
			$this->push_nodelist();
			$this->right = null;
			$this->else_nodelist = & $this->nodelist;
			$this->nodelist = array();
			break;
		
		default:
			parent::unknown_tag($tag, $params, $tokens);
			
			
		}
		
	}
	
	/**
	 * Pushes the current right value and nodelist into the nodelist stack
	 *
	 */
	function push_nodelist() {
		
		if (!is_null($this->right)) {
			$this->nodelists[] = array($this->right, $this->nodelist);
			
		} 
		
	}
	
	/**
	 * Renders the node
	 *
	 * @param LiquidContext $context
	 */
	
	function render(& $context) {
		
		$output = ''; // array();
		$run_else_block = true;
		
		foreach($this->nodelists as $data) {
			list($right, $nodelist) = $data;
			
			if ($this->equal_variables($this->left, $right, $context)) {
				$run_else_block = false;
				
				$context->push();
				$output .= $this->render_all($nodelist, $context);
				$context->pop();
				
			}
		}

		if ($run_else_block) {
			$context->push();
			$output .= $this->render_all($this->else_nodelist, $context);
			$context->pop();			
			
		}
	
		return $output;
		
	}
	
}

/**
 * Includes another, partial, template
 * 
 * @example
 * {% include 'foo' %}
 * 
 * Will include the template called 'foo'
 * 
 * {% include 'foo' with 'bar' %}
 * 
 * Will include the template called 'foo', with a variable called foo that will have the value of 'bar'
 * 
 * {% include 'foo' for 'bar' %}
 * 
 * Will loop over all the values of bar, including the template foo, passing a variable called foo
 * with each value of bar
 *
 * @package Liquid
 */
class IncludeLiquidTag extends LiquidTag {
	
	/**
	 * The name of the template
	 *
	 * @var string
	 */
	var $template_name;
	
	/**
	 * True if the variable is a collection
	 *
	 * @var bool
	 */
	var $collection;
	
	/**
	 * The value to pass to the child template as the template name
	 *
	 * @var mixed
	 */
	var $variable;
	
	/**
	 * The LiquidDocument that represents the included template
	 *
	 * @var LiquidDocument
	 */
	var $document;
	
	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param LiquidFileSystem $file_system
	 * @return IncludeLiquidTag
	 */
	function IncludeLiquidTag($markup, & $tokens, & $file_system) {
		$regex = new LiquidRegexp('/("[^"]+"|\'[^\']+\')(\s+(with|for)\s+('.LIQUID_QUOTED_FRAGMENT.'+))?/');
							
		if ($regex->match($markup)) {
			
			$this->template_name = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);
			
			if (isset($regex->matches[1])) {
				$this->collection = ($regex->matches[3] == "for");
				
				$this->variable = $regex->matches[4];

			}
			
			$this->extract_attributes($markup);
			
		} else {
			trigger_error("Error in tag 'include' - Valid syntax: include '[template]' (with|for) [object|collection]", E_USER_ERROR);
			
		}
		
		parent::LiquidTag($markup, $tokens, $file_system);
		
	}
	
	/**
	 * Parses the tokens
	 *
	 * @param array $tokens
	 */
	function parse($tokens) {
		if (!isset($this->file_system)) {
			trigger_error("No file system", E_USER_ERROR);
		} 
		
		// read the source of the template and create a new sub document
		$source = $this->file_system->read_template_file($this->template_name);
		$tokens = LiquidTemplate::tokenize($source);
		$this->document = new LiquidDocument($tokens, $this->file_system);
		
	}
	
	/**
	 * Renders the node
	 *
	 * @param LiquidContext $context
	 */
	function render(& $context) {
		$result = '';
		$variable = $context->get($this->variable);
		
		$context->push();
		
		foreach($this->attributes as $key => $value) {
			$context->set($key, $context->get($value));
			
		}
		
		if ($this->collection) {
			
			foreach($variable as $item) {
				$context->set($this->template_name, $item);
				$result .= $this->document->render($context);
			}
			
		} else {
			if (!is_null($this->variable)) {
				$context->set($this->template_name, $variable);
				
			}
			
			$result .= $this->document->render($context);
			
		}
		
		
		$context->pop();
		
		return $result;
		
		
	}
	
}
?>