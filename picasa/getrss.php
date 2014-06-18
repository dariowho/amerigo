<?php
	
	/**
	 * (IT) Semplice proxy per il feed RSS di Picasa: il browser potrebbe rifiutare 
	 * richieste Ajax verso domini diversi.
	 * 
	 * (EN) Just a proxy to Picasa's RSS feed: the browser may refuse cross-domain
	 * Ajax requests.
	 * 
	 */
	
	require_once('../config.inc.php');

	$user  = $conf['picasa.uid'];
	$album = $_GET['albumid'];
	
	$url = 'https://picasaweb.google.com/data/feed/api/user/'.$user.'/albumid/'.$album.'?alt=rss';
	
	$fp = fopen($url, 'r');
	
	fpassthru($fp);
		
	fclose($fp);
		
	exit(0);
	
?>
