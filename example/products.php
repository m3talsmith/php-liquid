<?php

class ProductsFilter {
	function price($integer) {
		return sprintf("$%.2d USD", $integer / 100);
	}
	
	function prettyprint($text) {
		return preg_replace('/\*(.*)\*/', '<b>\1</b>', $text);
	}
	
	function count($array) {
		return count($array);
	}
	
	function paragraph($p) {
		return "<p>".$p."</p>";
	}
}

require_once('../lib/liquid.php');

$liquid = new LiquidTemplate();
$liquid->register_filter(new ProductsFilter());
$liquid->parse(file_get_contents('templates/products.liquid'));

$products_list = array(
    array('name' => 'Arbor Draft', 'price' => 39900, 'description' => 'the *arbor draft* is a excellent product' ),
    array('name' => 'Arbor Element', 'price' => 40000, 'description' => 'the *arbor element* rocks for freestyling'),
    array('name' => 'Arbor Diamond', 'price' => 59900, 'description' => 'the *arbor diamond* is a made up product because im obsessed with arbor and have no creativity')
);

$products = array('products' => $products_list, 'section' => 'Snowboards', 'cool_products' => true);

$assigns = array('date' => date('r'));

print $liquid->render($products);

?>