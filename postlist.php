<html>
	<head>
	<meta charset="utf-8">
		
	<title>Tutti i post - Hello, World!</title>
	
	<link rel="stylesheet" type="text/css" href="semplice.css">
	
	<style>
		.post {
			display: block;
			margin-bottom: 1em;	
		}

		.titolo {
			display: block;
			font-style: italic;	
		}
		
		.sommario {
			display: block;
			font-style: italic;	
		}

	</style>
	
	</head>
	
	<body>
	<div id="header" style="z-index:2;">
		
		<a href="index.html"><img src="post/aspetto/logo.png" id="logo"></a>
		<h1>Tutti i post</h1>

	</div>
	
	<div id=content>
		<p>Questa pagina è qui principalmente per permettere a Google di <strong>indicizzare</strong> il sito: a meno che non ti interessi consultare i post in ordine cronologico, probabilmente ti troverai meglio sulla <a href="index.html">Pagina Principale</a>.</p>
		<?php $listHtmlRoot = 'post/'; require('post/listHtml.php'); ?>
	</div>

<div id="footer">
  <div id="footerMenu">
	<a href="index.html">Pagina Principale</a> - <a href="about.html">About</a> - Tutti i post - <a href="altriviaggi.html">Foto di altri viaggi</a>
  </div>

<div id="footerGithub">
	<a href="https://github.com/dario-chiappetta/amerigo" target="_blank">
		<img src="github-icon.png" style="height: 40px;"><br>All of this is Open Source: get the code on GitHub!
	</a>
</div>
	
	
	</body>
</html>


