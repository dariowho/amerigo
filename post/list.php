<?php
	foreach ( glob('meta/*.txt')  as $fn ) {
		echo trim( file_get_contents( $fn ) );
		echo "\n".preg_replace('/meta\\/([^\\/]+)\\.txt/', '$1', $fn);
		echo "\n\n\n";
	}
?>
