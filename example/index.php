<?php

require_once('../lib/liquid.php');

$liquid = new LiquidTemplate();
$liquid->parse(file_get_contents('templates/index.liquid'));

$assigns = array('date' => date('r'));

print $liquid->render($assigns);


?>