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
 * A Liquid file system is way to let your templates retrieve other templates for use with the include tag.
 *
 * You can implement subclasses that retrieve templates from the database, from the file system using a different 
 * path structure, you can provide them as hard-coded inline strings, or any manner that you see fit.
 *
 * You can add additional instance variables, arguments, or methods as needed
 * 
 * @package Liquid
 */
class LiquidBlankFileSystem {

	/**
	 * Retrieve a template file
	 *
	 * @param string $template_path
	 */
	function read_template_file($template_path) {
		trigger_error("This liquid context does not allow includes.", E_USER_ERROR);
		
	}
	
}

/**
 * This implements an abstract file system which retrieves template files named in a manner similar to Rails partials,
 * ie. with the template name prefixed with an underscore. The extension ".liquid" is also added.
 * 
 * For security reasons, template paths are only allowed to contain letters, numbers, and underscore.
 * 
 * @package Liquid
 */
class LiquidLocalFileSystem extends LiquidBlankFileSystem {
	
	/**
	 * The root path
	 *
	 * @var string
	 */
	var $root;
	
	/**
	 * Conctructore
	 *
	 * @param string $root The root path for templates
	 * @return LiquidLocalFileSystem
	 */
	function LiquidLocalFileSystem($root) {
		$this->root = $root;
		
	}
	
	/**
	 * Retrieve a template file
	 *
	 * @param string $template_path
	 * @return string
	 */
	
	function read_template_file($template_path) {
		$full_path = $this->full_path($template_path);
		
		if ($full_path) {
			file_get_contents($full_path);
		} else {
			trigger_error("No such template '$template_path'", E_USER_ERROR);
			return false;
		}
		
	}
	
	/**
	 * Resolves a given path to a full template file path, making sure it's valid
	 *
	 * @param string $template_path
	 * @return string
	 */
	function full_path($template_path) {
		$name_regex = new LiquidRegexp('/^[^.\/][a-zA-Z0-9_\/]+$/');
		
		if (!$name_regex->match($template_path)) {
			trigger_error("Illegal template name '$template_path'", E_USER_ERROR);
			return false;
		}
		
		if (strpos($template_path, '/') !== false) {
			$full_path = $this->root.dirname($template_path).'/'."_".basename($template_path).".liquid";
			
		} else {
			$full_path = $this->root."_".$template_path.".liquid";
			
		}
		
		$root_regex = new LiquidRegexp(realpath($root));
		
		if (!$root_regex->match(realpath($full_path))) {
			trigger_error("Illegal template path '".realpath($full_path)."'", E_USER_ERROR);
		} else {
			return $full_path;
		}
		
	}
	
}