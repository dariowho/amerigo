<?php

	// Kinda ugly, but I'm kinda rushing...
	$root = (!empty($listHtmlRoot)) ? $listHtmlRoot : '';

	require($root.'_common.inc.php');

	foreach ( glob($root.'meta/*.txt')  as $fn ) {
		$id = preg_replace('/.*meta\\/([^\\/]+)\\.txt/', '$1', $fn);
		$post = split("\n", trim( file_get_contents( $fn ) ) );
		
		echo "<span class='post'>\n";
		echo "	<span class='titolo'><a href='${root}view.php?id=$id'>".$post[$META_TITOLO]."</a></span>\n";
		echo "	<span class='sommario'>".$post[$META_SOMMARIO]."</span>\n";
		echo "	<span class='data'>".$post[$META_DATA]."</span>\n";
		echo "</span>\n";
	}
?>
