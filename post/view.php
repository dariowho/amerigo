<?php

/**
 * (IT) Questa pagina mostra il contenuto di un post. I metadati del post sono 
 * caricati dal file 'meta/<POST-ID>.txt'; il testo del post dal file
 * 'body/<POST-ID>.wiki.txt'.
 * 
 * I metadati contengono l'ID di un album Picasa. Questo sarà usato per mostrare
 * sulla mappa le foto contenute nell'album.
 * 
 * Il testo del post può essere formattato usando HTML e/o markup Wiki. Il parsing 
 * della sintassi Wiki avviene lato client, tramite lo script di Remy Sharp, che 
 * è scaricabile da: http://remysharp.com/2008/04/01/wiki-to-html-using-javascript/
 * 
 * -----
 * 
 * (EN) This page shows the content of a post. The post's meta-data is loaded 
 * from the file 'meta/<POST-ID>.txt'; the post's body is loaded from the file 
 * 'body/<POST-ID>.wiki.txt'.
 * 
 * Meta-data contains also a Picasa Web Album ID. This is used to display the 
 * album's pictures on the map.
 * 
 * The post's body can be formatted using HTML and/or Wiki markup. The parsing 
 * of the latter happens at client-side, with Remy Sharp's script, which can be 
 * found at: http://remysharp.com/2008/04/01/wiki-to-html-using-javascript/
 * 
 */ 

require_once('../config.inc.php');
require_once('_common.inc.php');

// Must get an ID
if (! isset($_GET['id']) )
	return returnError();

// ID must be only letters/digits/dashes/underscores
if (!preg_match('/^[0-9a-z\-\_]+$/i', $_GET['id']))
	return returnError();

$id = $_GET['id'];

// Post files must exist
$metaFile = 'meta/'.$id.constant('ESTENSIONE_META');
$bodyFile = 'body/'.$id.constant('ESTENSIONE_BODY');
if (!file_exists($metaFile) || !file_exists($bodyFile) )
	return returnNotfound();

$meta = file_get_contents( $metaFile );
$body = file_get_contents( $bodyFile );

$meta = split("\n",$meta);

?>


<html>
	<head>
	<meta charset="utf-8">
		
	<title><?php echo $meta[$META_TITOLO] . ' - ' . $conf['site.title']; ?></title>
	
	<link rel="stylesheet" type="text/css" href="aspetto/stile.css">
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
	
	<script src="wiki2html.js" type="text/javascript" charset="utf-8"></script>
	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>

	</head>
	
	<body>
	<div id="tag" style="z-index:2;">
		
	<div style="position:absolute; top:0; color:white; font-size: 14px; width:250px; text-align:center;" id="loading">
		<div id="loadingText">Sto caricando le foto...</div>
		<img src="../ajax-loader.gif" style="vertical-align: middle;">
	</div>
	
		<a href="../index.html"><img src="aspetto/logo.png" id="logo"></a>
		<div id="data">
			<span class="giorno">01</span><br>
			<span class="mese">Settembre</span><br>
			<span class="anno">2014</span>
		</div>
	</div>

	<div id="titolo">
	<h2><?php echo $meta[$META_TITOLO]; ?></h2>
	</div>
	
	<div id="map-container" style="z-index:1;">
		<div id="map" style="z-index:1; background:white;"></div>
	</div>
	
		
	<div id=content>
	<?php echo $body; ?>
	</div>
	
	
<?php
	if ($conf['adsense.enabled']) {
		
?>
	<!-- AdSense -->
	<div style="text-align:center;">
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- helloworld -->
		<ins class="adsbygoogle"
			 style="display:inline-block;width:728px;height:90px"
			 data-ad-client="ca-pub-9057555078598955"
			 data-ad-slot="9313787485"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	</div>
<?php
	
	}
?>	



<?php
	if ($conf['livefyre.enabled']) {
		
?>
	<!-- START: Livefyre Embed -->
	<div id="livefyre-comments" style="padding:1em; margin-bottom:50px;"></div>
	<script type="text/javascript" src="http://zor.livefyre.com/wjs/v3.0/javascripts/livefyre.js"></script>
	<script type="text/javascript">
	(function () {
		var articleId = "<?php echo $id; ?>";
		fyre.conv.load({}, [{
			el: 'livefyre-comments',
			network: "livefyre.com",
			siteId: "<?php echo $conf['livefyre.siteid']; ?>",
			articleId: articleId,
			signed: false,
			collectionMeta: {
				articleId: articleId,
				url: fyre.conv.load.makeCollectionUrl(),
			}
		}], function() {});
	}());
	</script>
	<!-- END: Livefyre Embed -->
<?php
	
	}
?>	

	<!-- Parse the wiki markup -->
	<script>
		var wikiContents = document.getElementById('content');
		wikiContents.innerHTML = wikiContents.innerHTML.wiki2html();
	</script>
	
	<!-- Render the map -->
	<script type="text/javascript" > 	
	var lat = <?php echo htmlentities($meta[$META_LATITUDINE]); ?>;
	var lng = <?php echo htmlentities($meta[$META_LONGITUDINE]); ?>;
	
	var map = L.map('map').setView([lat, lng], 6);

	// Tile layer (Mapquest)
	var tl = L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpeg', {
		attribution: 'Tiles Courtesy of <a href="http://www.mapquest.com/">MapQuest</a> &mdash; Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
		subdomains: '1234'
	});
	tl.addTo(map);
	
	// Load the pictures from Picasa
	var marker;
	$.get('../picasa/getrss.php?albumid=<?php echo $meta[$META_ALBUMID]; ?>', function (data) {
		$(data).find("item").each(function () {
			var el = $(this);
			
			// Only if picture has no geo data
			if ( el.find("gml\\:pos").length != 0 ) {
				// Album data from feed
				var imageurl    = el.find("enclosure").attr('url');
				var markerurl   = imageurl.replace(/([^\/]+$)/g, "s40/TEST$1");
				var markerlink  = imageurl.replace(/([^\/]+$)/g, "s0/TEST$1");
				var polaroidurl = "../polaroid/polaroid.php?key=2394872&url="+markerurl;
				var latLng      = el.find("gml\\:pos").text().split(" ");

				// The Marker
				var icon = L.icon({
					iconUrl: polaroidurl,
					iconAnchor:  [20, 20] // TODO: this is not the exact center (default would be top-left corner)
				});
				var marker = L.marker([latLng[0],latLng[1]], {icon: icon}).addTo(map);
				
				// The Infobox
				var divNode = document.createElement('DIV');
				divNode.innerHTML = '<a href="'+markerlink+'" target="_blank"><img src="'+imageurl+'" border="0"></a>';
				marker.bindPopup(divNode, {
					maxWidth:600,
					minWidth:100 });
			}

		});
		
		$("#loadingText").text("Fatto!"); $("#loading").fadeOut(2000);
	});
	
	var markerPost = L.marker([lat,lng]).addTo(map);
	</script>
	
<div id="footer">
		  <div style="font-family: maven; overflow: hidden; display: none;" id="loading">
  <img src="../ajax-loader.gif"> <div id="loadingText">Fatto!</div></div>
  
  <div id="footerMenu">
	<a href="../index.html">Pagina Principale</a> - <a href="../about.html">About</a> - <a href="../postlist.php">Tutti i post</a> - <a href="../altriviaggi.html">Foto di altri viaggi</a>
  </div>

<div id="footerGithub">
	<a href="https://github.com/dario-chiappetta/amerigo" target="_blank">
		<img src="../github-icon.png" style="height: 40px;"><br>All of this is Open Source: get the code on GitHub!</div>
	</a>
</div>
	
	
	</body>
</html>


<?php


function returnError() {
?>
	<h1>Errore</h1>
	<p>Parametro invalido o mancante: id. <a href="mailto:dario.chiappetta@outlook.com">Scrivimi</a> per segnalare l'errore.</p>
	<p>Invalid or missing parameter: id. <a href="mailto:dario.chiappetta@outlook.com">Drop me a line</a> to report the error.</p>
<?php
	return 1;
}

function returnNotfound() {
	global $id;
?>
	<h1>Post non trovato!</h1>
	<p>Sembra che non ci sia nessun post con id '<tt><?php echo $id; ?></tt>'. <a href="mailto:dario.chiappetta@outlook.com">Scrivimi</a> per segnalare l'errore.</p>
	<p>No post with id '<tt><?php echo $id; ?></tt>' exists in the system. <a href="mailto:dario.chiappetta@outlook.com">Drop me a line</a> to report the error.</p>
<?php
	return 1;
}

?>
