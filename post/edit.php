<?php

/**
 * (IT) Tramite questa pagina è possibile creare un nuovo post, o modificarne uno 
 * esistente.
 * 
 * NOTA: Per accedere a questa funzione è necessario fornire il parametro 'pass'
 * tramite GET (es. 'http://il-tuo-blog.it/post/edit.php?pass=<LA-TUA-PASSWORD>'),
 * dove la password è quella definita nel file di configurazione in radice.
 * Non è il modo più sicuro del mondo per autenticarsi, ma non ho tempo, di 
 * implementarne uno più sofisticato al momento...
 * 
 * NOTA: L'id del post (nuovo o esistente) deve essere fornito tramite GET, 
 * come la password. Una tipica richiesta di questa pagina sarà quindi del tipo:
 * 
 * http://il-tuo-blog.it/post/edit.php?pass=<LA-TUA-PASSWORD>&id=<ID-DEL-POST>
 * 
 * L'id di un post può contenere solo lettere, numeri, trattini e underscore.
 * 
 * -----
 * 
 * (EN) This page is used to create a new post, or edit an existing one.
 * 
 * NOTE: To access this page you need to provide your password as a GET parameter 
 * 'pass' (e.g. http://il-tuo-blog.it/post/edit.php?pass=<LA-TUA-PASSWORD>), 
 * where the password is the one you have set in the root configuration file.
 * This isn't the last word in terms of security, but I haven't got time for 
 * fancy stuff now
 * 
 * NOTE: The post ID must be given via GET as well. The typical request will thus 
 * be something like
 * 
 * http://il-tuo-blog.it/post/edit.php?pass=<LA-TUA-PASSWORD>&id=<ID-DEL-POST>
 * 
 * Only letters, numbers, dashes and underscores are allowed in a post id
 *  
 */ 

require_once('../config.inc.php');
require_once('_common.inc.php');

if ( !isset($_GET['pass']) || $_GET['pass'] != $conf['PASS'] )
	die('Non autorizzato');
	
if ( !isset($_GET['id']) || !isValidId($_GET['id']) )
	die('Parametro invalido o mancante: id');
	
$id = $_GET['id'];
$bodyFilename = 'body/'.$id.constant('ESTENSIONE_BODY');
$metaFilename = 'meta/'.$id.constant('ESTENSIONE_META');


$titolo   = '';
$sommario = '';
$data     = '';
$lat      = '';
$lng      = '';
$albumid  = '';
$stato    = '1';
$body     = '';
$overwrite = '1';

?>

<html>
	<head>
	<meta charset="utf-8">
	<title>Crea o modifica un post</title>
	</head>
	
	<body>

<?php

	// (IT) Tutti i dati presenti: scrivi il post
	// (EN) All the data is available: write post
	if (
	     !empty($_POST['titolo']) && 
	     !empty($_POST['sommario']) && 
	     !empty($_POST['data']) && 
	     !empty($_POST['lat']) && 
	     !empty($_POST['lng']) && 
	     !empty($_POST['albumid']) &&
	     !empty($_POST['stato'])
	   ) {
		   
		$titolo   = $_POST['titolo'];
		$sommario = $_POST['sommario'];
		$data     = $_POST['data'];
		$lat      = ( is_numeric($_POST['lat']) ) ? $_POST['lat'] : FALSE;
		$lng      = ( is_numeric($_POST['lng']) ) ? $_POST['lng'] : FALSE;
		$albumid  = ( is_numeric($_POST['albumid']) ) ? $_POST['albumid'] : FALSE;
		$stato    = ( is_numeric($_POST['stato']) ) ? $_POST['stato'] : FALSE;

		$body = $_POST['body'];

		$overwrite = ( is_numeric($_POST['overwrite']) ) ? $_POST['overwrite'] : FALSE;
		
		if (! $lat || ! $lng || ! $albumid || $stato === FALSE || $overwrite === FALSE) {
			echo "<h1>Parametri non validi: il post non è stato creato o modificato</h1>";
		} else {
			
			if ( (! file_exists($metaFilename) && ! file_exists($bodyFilename) ) || $overwrite == 1) {
				$rMeta = file_put_contents( $metaFilename, $titolo."\n".
				                                           $sommario."\n".
				                                           $data."\n".
				                                           $lat."\n".
				                                           $lng."\n".
				                                           $albumid."\n".
				                                           $stato );

				if ( ! $rMeta ) {
					echo "<p>Impossibile scrivere i metadati del post.</p>";
				} else {
					$rBody = file_put_contents( $bodyFilename, $body );
					if ( ! $rBody )
						echo "<p>Impossibile scrivere il testo del post.</p>";
					else {
						echo "<h1>Post scritto correttamente!</h1>\n";
						echo "<p>Clicca <a href='view.php?id=$id'>qui</a> per visualizzarlo.</p>\n";
						
						$id = FALSE;	// Will prevent form from showing
					}
				}
				
			} else {
				echo "<p>Il parametro 'overwrite' dev'essere 1 per sovrascrivere un post esistente.</p>";
			}
		}

	// (IT) Carica eventuali dati esistenti
	// (EN) Try and fetch existing data
	} else {
		
		if ( file_exists($metaFilename) && file_exists($bodyFilename) ) {
			$meta = file_get_contents( $metaFilename );
			$body = file_get_contents( $bodyFilename );
			$meta = split("\n",$meta);
			
			$titolo    = $meta[ $META_TITOLO ];
			$sommario  = $meta[ $META_SOMMARIO ];
			$data      = $meta[ $META_DATA ];
			$lat       = $meta[ $META_LATITUDINE ];
			$lng       = $meta[ $META_LONGITUDINE ];
			$albumid   = $meta[ $META_ALBUMID ];
			$stato     = $meta[ $META_STATO ];
			
			$overwrite = '0';
		}

	}
	
	// (IT) Mostra il modulo di inserimento
	// (EN) Print the input form
	if ($id) {
		$titolo    = ( !empty($_POST['titolo']) )   ? $_POST['titolo']   : $titolo;
		$sommario  = ( !empty($_POST['sommario']) ) ? $_POST['sommario'] : $sommario;
		$data      = ( !empty($_POST['data']) )     ? $_POST['data']     : $data;
		$lat       = ( !empty($_POST['lat']) )      ? $_POST['lat']      : $lat;
		$lng       = ( !empty($_POST['lng']) )      ? $_POST['lng']      : $lng;
		$albumid   = ( !empty($_POST['albumid']) )  ? $_POST['albumid']  : $albumid;
		$stato     = ( !empty($_POST['stato']) )    ? $_POST['stato']    : $stato;	
		$body      = ( !empty($_POST['body']) )     ? $_POST['body']     : $body;	
		
?>		
		<form action="edit.php?pass=<?php echo $conf['PASS']; ?>&id=<?php echo $id; ?>" method="POST">
			Titolo <input type="text" name="titolo" id="inputTitolo" value="<?php echo $titolo; ?>"><br>
			Sommario <input type="text" name="sommario" id="inputSommario" value="<?php echo $sommario; ?>"><br>
			Data <input type="text" name="data" id="inputData" value="<?php echo $data; ?>"><br>
			Latitudine <input type="text" name="lat" id="inputLat" value="<?php echo $lat; ?>"><br>
			Longitudine <input type="text" name="lng" id="inputLng" value="<?php echo $lng; ?>"><br>
			Picasa Album ID <input type="text" name="albumid" id="inputAlbumid" value="<?php echo $albumid; ?>"><br>
			Stato <input type="text" name="stato" id="inputStato" value="<?php echo $stato; ?>"><br>
			Overwrite <input type="text" name="overwrite" id="inputOverwrite" value="<?php echo $overwrite; ?>"><br>
			<textarea name="body"><?php echo $body; ?></textarea>
			<input type="submit">
		</form>
		
		<hr>
		
		Data di oggi: <span id="dataOggi"></span><br>
		Latitudine: <span id="latitudineQui"></span><br>
		Longitudine: <span id="longitudineQui"></span><br>
		Picasa Drop Box ID: 6022558561768337697<br>
		
		<script>
		var dataOggi       = document.getElementById("dataOggi");
		var latitudineQui  = document.getElementById("latitudineQui");
		var longitudineQui = document.getElementById("longitudineQui");
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		}
			
		function showPosition(position) {
			latitudineQui.textContent = position.coords.latitude;
			longitudineQui.textContent = position.coords.longitude;
		}
		
		// Fill the date
		var now    = new Date();
		var giorno = ("0" + now.getDate()).slice(-2);
		var mese   = ("0" + (now.getMonth() + 1)).slice(-2);
		var anno   = now.getFullYear();
		var ore    = now.getHours();
		var minuti = now.getMinutes();
		var zona   = now.getTimezoneOffset()/60;
		dataOggi.textContent = giorno+"/"+mese+"/"+anno+", "+ore+":"+minuti+" (UTC "+zona+")";
		</script>
<?php
	}
?>
	</body>
</html>
