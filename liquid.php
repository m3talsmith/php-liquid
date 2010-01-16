<?php

/**
 * Liquid for PHP
 * 
 * LICENSE
 * 
 * Permission is hereby granted, free of charge, to any person 
 * obtaining a copy of this software and associated documentation 
 * files (the "Software"), to deal in the Software without 
 * restriction, including without limitation the rights to use, 
 * copy, modify, merge, publish, distribute, sublicense, and/or 
 * sell copies of the Software, and to permit persons to whom 
 * the Software is furnished to do so, subject to the following 
 * conditions:
 * 
 * The above copyright notice and this permission notice shall 
 * be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE.
 * 
 * @package Liquid
 * @copyright Copyright (c) 2006 Mateo Murphy, 
 * based on Liquid for Ruby (c) 2006 Tobias Luetke
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 */

/**
 * The method is called on objects when resolving variables to see
 * if a given property exists
 *
 */
define('LIQUID_HAS_PROPERTY_METHOD', 'field_exists');

/**
 * This method is called on object when resolving variables when
 * a given property exists
 *
 */
define('LIQUID_GET_PROPERTY_METHOD', 'get');

/**
 * Seperator between filters
 *
 */
define('LIQUID_FILTER_SEPERATOR', '\|');

/**
 * Seperator for arguments
 *
 */
define('LIQUID_ARGUMENT_SPERATOR', ',');

/**
 * Seperator for argument names and values
 *
 */
define('LIQUID_FILTER_ARGUMENT_SEPERATOR', ':');

/**
 * Seperator for variable attributes
 *
 */
define('LIQUID_VARIABLE_ATTRIBUTE_SEPERATOR', '.');

/**
 * Tag start
 *
 */
define('LIQUID_TAG_START', '{%');

/**
 * Tag end
 *
 */
define('LIQUID_TAG_END', '%}');

/**
 * Variable start
 *
 */
define('LIQUID_VARIABLE_START', '{{');

/**
 * Variable end
 *
 */
define('LIQUID_VARIABLE_END', '}}');

/**
 * The characters allowed in a variable
 *
 */
define('LIQUID_ALLOWED_VARIABLE_CHARS', '[a-zA-Z_.-]');

/**
 * Regex for quoted fragments
 *
 */
define('LIQUID_QUOTED_FRAGMENT', '"[^"]+"|\'[^\']+\'|[^\s,|]+');

/**
 * Regex for recongnizing tab attributes
 *
 */
define('LIQUID_TAG_ATTRIBUTES', '/(\w+)\s*\:\s*('.LIQUID_QUOTED_FRAGMENT.')/');

/**
 * Regex used to split tokenss
 *
 */
define('LIQUID_TOKENIZATION_REGEXP', '/('.LIQUID_TAG_START.'.*?'.LIQUID_TAG_END.'|'.LIQUID_VARIABLE_START.'.*?'.LIQUID_VARIABLE_END.')/');


require_once('lib/support.php');
require_once('lib/block.php');
require_once('lib/context.php');
require_once('lib/document.php');
require_once('lib/drop.php');
require_once('lib/file_system.php');
require_once('lib/filterbank.php');
require_once('lib/htmltags.php');
require_once('lib/standardfilters.php');
require_once('lib/standardtags.php');
require_once('lib/tag.php');
require_once('lib/template.php');
require_once('lib/variable.php');

?>
