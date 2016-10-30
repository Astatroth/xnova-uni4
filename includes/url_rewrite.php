<?php

$rules[] = array(
	'source'  => '/^(.+)\.html/i',
	'target'  => 'content/article/{1}',
	'action'  => 'rewrite'
);

$rules[] = array(
	'source'  => '/^uni4\/(.+)/i',
	'target'  => '{1}',
	'action'  => 'rewrite'
);

?>