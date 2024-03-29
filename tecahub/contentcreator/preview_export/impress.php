<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=1024" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title>Deck Title</title>

<meta name="description" content="TODO" />
<meta name="author" content="TODO" />

<style>
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
</style>

<link rel="stylesheet" href="css/themes/default-reset.css"></link>
<link href="css/main.css" rel="stylesheet" type='text/css' />
<link rel="stylesheet" href="reveal/css/theme/default.css" id="theme">
<link href='css/web-fonts.css' rel='stylesheet' type='text/css' />
<link href='../styles/strut.themes/backgroundClasses.css' rel='stylesheet' type='text/css' />
<link href='../styles/strut.themes/surfaceClasses.css' rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/KaTeX/0.6.0/katex.min.css">
<link rel="shortcut icon" href="favicon.png" type='image/png' />
<link rel="apple-touch-icon" href="apple-touch-icon.png" type='image/png' />

<style>
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

<script type="text/javascript" src="scripts/dataset-shim.js"></script>
<script type="text/javascript" src="scripts/impress.js"></script>

<script src="scripts/onready.js"></script>
<script src="scripts/loadPresentation.js"></script>
<script>
ready(function() {
    if (document.getElementById('launched-placeholder'))
        loadPresentation();

    if (!window.presStarted) {
        startPres(document, window);
        impress().init();
    }

    if ("ontouchstart" in document.documentElement) {
        document.querySelector(".hint").innerHTML =
            "<p>Tap on the left or right to navigate</p>";
    }
});
</script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-42322531-1', 'strut.io');
    ga('send', 'pageview');
    </script>
</head>
<body class="impress-not-supported">
    <div id="launched-placeholder"></div>
</body>
</html>
