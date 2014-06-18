<?php
	
	define('ESTENSIONE_META', '.txt');
	define('ESTENSIONE_BODY', '.wiki.txt');
	
	$META_TITOLO      = 0;
	$META_SOMMARIO    = 1;
	$META_DATA        = 2;
	$META_LATITUDINE  = 3;
	$META_LONGITUDINE = 4;
	$META_ALBUMID     = 5;	// Picasa album ID
	$META_STATO       = 6;	// 0: non pubblicato, 1: pubblicato
	
	function isValidId( $id ) {
		return ( preg_match('/^[0-9a-z\-\_]+$/i', $id) ) ? TRUE : FALSE;
	}
?>
