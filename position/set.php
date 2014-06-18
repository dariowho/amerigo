<?php

/**
 * (IT) Questa pagina serve a comunicare al sistema la tua posizione corrente,
 * che sarà mostrata sulla mappa in home page (marker rosso). Puoi scegliere di 
 * salvare questa posizione nella cronologia. In questo caso la posizione rimarrà
 * sulla mappa come punto del tuo itinerario (linea rossa)
 * 
 * NOTA: Per accedere a questa funzione è necessario fornire il parametro 'pass'
 * tramite GET (es. 'http://il-tuo-blog.it/position/set.php?pass=<LA-TUA-PASSWORD>'),
 * dove la password è quella definita nel file di configurazione in radice.
 * Non è il modo più sicuro del mondo per autenticarsi, ma non ho tempo, di 
 * implementarne uno più sofisticato al momento...
 * 
 * -----
 * 
 * (EN) This page can be used to save your current position into the system. It 
 * will be showed on the home page's map as a red marker. You can choose to store 
 * it in the position history. In this case it will stay on the map as a point of 
 * your itinerary (the red line).
 * 
 * NOTE: To access this page you need to provide your password as a GET parameter 
 * 'pass' (e.g. http://il-tuo-blog.it/position/set.php?pass=<LA-TUA-PASSWORD>), 
 * where the password is the one you have set in the root configuration file.
 * This isn't the last word in terms of security, but I haven't got time for 
 * fancy stuff now
 * 
 */

require_once('../config.inc.php');

if (! isset($_GET['pass']) || $_GET['pass'] != $conf['PASS'] )
	die('Non autorizzato');
?>

<html>
	<head>
	<title>Indica la tua ultima posizione</title>
	</head>
	
	<body>

<?php
	define('FILE_LAST', 'last.txt');
	define('FILE_HISTORY', 'cronologia.txt');

	if ( isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET['date']) && isset($_GET['inhistory'])) {
		$lat = ( is_numeric($_GET['lat']) ) ? $_GET['lat'] : FALSE;
		$lng = ( is_numeric($_GET['lng']) ) ? $_GET['lng'] : FALSE;
		$date = $_GET['date'];	// TODO: input check
		
		if (! $lat || ! $lng ) {
			echo "<h1>Parametri non validi: posizione non aggiornata</h1>";
		} else {
			$r = file_put_contents( constant('FILE_LAST'), $lat."\n".$lng."\n".$date );
			
			if ($r)
				echo "<h1>Posizione aggiornata correttamente</h1>";
			else
				echo "<h1>Impossibile aggiornare la posizione</h1>";
			
			$r = FALSE;
			if ( $_GET['inhistory'] == 1) {
				$r = file_put_contents( constant('FILE_HISTORY'), $lat."\n".$lng."\n".$date."\n\n\n", FILE_APPEND );
				if ( $r )
					echo "<p>Il file della cronologia &egrave; stato aggiornato.</p>";
			}
			
			if (! $r )
				echo "<p>Il file della cronologia non &egrave; stato aggiornato.</p>";
		}

	} else {
?>		
		<form action="set.php" method="GET">
			Latitudine <input type="text" name="lat" id="inputLat"><br>
			Longitudine <input type="text" name="lng" id="inputLng"><br>
			Data <input type="text" name="date" id="inputDate"><br>
			Salva cronologia <input type="text" name="inhistory" id="inputInHistory" value="0"><br>
			<input type="hidden" name="pass" value="<?php echo $conf['PASS']; ?>">
			<input type="submit">
		</form>
		
		<script>
		var inputLat = document.getElementById("inputLat");
		var inputLng = document.getElementById("inputLng");
		var inputDate = document.getElementById("inputDate");
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		}
			
		function showPosition(position) {
			inputLat.value = position.coords.latitude;
			inputLng.value = position.coords.longitude;
		}
		
		// Fill the date
		var now = new Date();
		inputDate.value = now.toString();
		</script>
		
<?php
	}
?>
	</body>
</html>
