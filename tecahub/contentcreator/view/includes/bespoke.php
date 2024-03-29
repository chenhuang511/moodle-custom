<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<title>Deck Title</title>
	<link rel="stylesheet" href="css/themes/default-reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css" />
	<link rel="stylesheet" href="reveal/css/theme/default.css" id="theme">
	<link href='css/web-fonts.css' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" type="text/css" href="bespoke/css/demo.css" />
	<link rel="stylesheet" type="text/css" href="bespoke/css/themes.css" />
	<link href='../styles/strut.themes/backgroundClasses.css' rel='stylesheet' type='text/css' />
	<link href='../styles/strut.themes/surfaceClasses.css' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.css">

	<style type="text/css">
	.componentContainer {
	    position: absolute;
	    line-height: normal;
	    /*-webkit-transform-origin: 135% 135%;
	    -moz-transform-origin: 135% 135%;
	    transform-origin: 135% 135%;*/
	}

	.transformContainer {
	    -webkit-transform-origin: 0 0;
	    -moz-transform-origin: 0 0;
	    transform-origin: 0 0;
	}

	.bg {
	    width: 100%;
	    height: 100%;
	}

	#themeChooser {
		position: absolute;
		z-index: 1;
		background-color: #EEE;
		border-radius: 5px;
		padding: 10px;
	}

	#main, body {
		overflow: hidden;
	}

	.reveal.themedArea {
	    display: block;
	    position: absolute;
	    top: 0px;
	    left: 0px;
	    z-index: 0;
	    width: 100%;
	    height: 100%;
	}
	</style>

	<script src="bespoke/scripts/bespoke.js"></script>
	<script src="bespoke/scripts/bespoke-plugins.js"></script>
	<script src="scripts/onready.js"></script>
	<script>
	ready(function() {
		if (!window.presStarted) {
    		bespoke.horizontal.from('article', {
    			hash: true,
    			state: true
    		});
		}
	});
	</script>
</head>
<body>
<?php

echo $contenthtml;

?>

</body>
</html>
