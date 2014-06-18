<?php

	/**
	 * (IT) Il compito di questa pagina è generare i marker che saranno 
	 * visualizzati sulle mappe. In particolare, la pagina riceve l'URL di 
	 * un'immagine, aggiunge una cornice bianca, l'ombra, una leggera rotazione 
	 * e restituisce l'immagine risultante in formato PNG.
	 * 
	 * NOTA: come blanda misura di sicurezza, è richiesto un parametro di nome 
	 * 'key', e valore '2394872'
	 * 
	 * NOTA: L'immagine restituita avrà le stesse dimensioni di quella ricevuta 
	 * (al netto di cornice e rotazione).
	 * 
	 * -----
	 * 
	 * (EN) The purpose of this page is to generate the markers that will be shown 
	 * on the blog's maps. Especially, the page receives an image URL, adds a 
	 * white frame, shadow, a mild rotation and returns the result in PNG format.
	 * 
	 * NOTE: as a shallow securiy measure, a 'key' parameter is needed, with 
	 * value '2394872'
	 * 
	 * NOTE: The returned image will have the same size as the input one (plus
	 * frame and rotation) 
	 * 
	 */

	// This script returns an image: text is not allowed (use logs instead)
	error_reporting(0);

	/*
	 *  Constants
	 */

	define('ERRORE', 'e.png');			// Will be returned in case of error
	define('CACHE_FOLDER', '_cache');	// Will store cached images
	define('CACHE_PERM', 0777);			// Permissions in the cache folder
	define('ERROR_LOG', 'errori.log');	// Log file


	/*
	 *  Main
	 */

	// Parameters handling
	if (! isset($_GET['key']) || $_GET['key'] != 2394872 )
		die('Not authorized');
	$url = $_GET['url'];
	
	// If cache item exists, return it
	$cachePath = getCachePath( $url );
	if ( file_exists($cachePath) )
		returnImage( $cachePath );
		
	// Otherwise, generate Polaroid and cache it
	$polaroid = generatePolaroid( $url );
	if (! $polaroid)
		returnImage( constant('ERRORE') );
	$polaroid->writeImage( $cachePath );
	returnImage( $cachePath );

	exit(0);
	
	
	
	/*
	 *  Functions
	 */
	
	function generatePolaroid( $url ) {
		$fp = fopen($url, 'rb');

		if ($fp == FALSE) {
			logError( 'Impossibile aprire l\'URL: '.$url );
			return FALSE;
		}
		
		$img = new Imagick();
		$img->readImageFile($fp);
		
		$img->setImageFormat('png');
		
		$img->borderImage('white', 4, 4);
		$img->borderImage('grey60', 1, 1);
		$img->rotateImage('none', rand(-5,5));
		
		$shadow = clone $img;
		$shadow->setImageBackgroundColor( 'black' ); 
		$shadow->shadowImage( 50, 1, 4, -40 ); 
		
		$shadow->compositeImage( $img, Imagick::COMPOSITE_OVER, 0, 0 ); 
		
		//~ header("Content-Type: image/png");
		//~ echo $shadow;

		fclose($fp);
		
		return $shadow;
	}

	function returnImage( $path ) {
		$fp = fopen($path, 'rb');
		
		if ($fp == FALSE) {
			logError('Impossibile aprire l\'immagine locale: '.$path);
			die("bad shit happening");
		}
		
		header("Content-Type: image/png");

		fpassthru($fp);
		
		fclose($fp);
		
		exit;
	}

	/**
	 * (IT) Ritorna il path del risultato in cache relativo al dato URL. Il path 
	 * restituito include il nome del file, ed è relativo alla working directory.
	 * Se il path non esiste, crea ricorsivamente le cartelle necessarie (ma non
	 * il file in cache!)
	 * 
	 * (EN) Returns a path to the cached item corresponding to the given URL.
	 * The path includes the filename, and is relative to the working directory.
	 * If the path doesn't exist, recursively creates the necessary directories
	 * (not the cache item!)
	 * */
	function getCachePath( $url ) {
		// Trim protocol and trailing slash
		$url = preg_replace('/(.+:\/\/)/', '', $url);
		$url = rtrim($url,"/");

		// Separate filename 
		$m = preg_match('/(.*(?=\/))\/([^\/]+)$/', $url, $matches);
		if ($m == FALSE || $m == 0) {
			logError('Impossibile ricavare cache file da URL: '.$url);
			return constant('ERRORE_IMG') ;
		}
		$path     = $matches[1];
		$filename = $matches[2];
		
		// Detect errors
		if ( empty($path) || empty($filename) || strpos($path, '..') || strpos($filename, '..') ) {
			logError('Path o nome file mancante o non valido in URL (non è consentito usare \'..\'):'.$url);
			return constant('ERRORE_IMG');
		}
			
		// Add default cache directory and file format
		$path = joinPaths( constant('CACHE_FOLDER'), $path);
		$filename .= '.png';
		
		// Create folder if doesn't exist
		if (! file_exists($path) )
			if (! mkdir($path, constant('CACHE_PERM'), TRUE) ) {
				logError('Impossibile creare le cartelle necessarie in cache: '.$path);
				return constant('ERRORE_IMG');
			}
		
		return joinPaths( $path, $filename );
	}

	/**
	 * (IT) Unisce due path (formato UNIX)
	 * 
	 * (EN) Joins two file paths (UNIX format)
	 * 
	 * Fonte/Source:
	 * http://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
	 * */
	function joinPaths() {
		$args = func_get_args();
		$paths = array();
		foreach ($args as $arg) {
			$paths = array_merge($paths, (array)$arg);
		}

		$paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
		$paths = array_filter($paths);
		return join('/', $paths);
	}
	
	function logError($message) {
		date_default_timezone_set("Europe/Rome");
		error_log("[".date('d/m/Y, H:i:s')."] $message\n", 3, constant('ERROR_LOG'));
	}

?>
