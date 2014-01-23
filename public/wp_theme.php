<!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Kortingcode Blog</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		<link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/public/css/front_end/style.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/public/js/common.js"></script>
		<script type="text/javascript" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/public/js/front_end/layout.js"></script>
	</head>
	<body>
		<div class="innerwrapper">
			<?php
				// get the header from the zend framework app
				echo file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/wordpress/getheader');

				// get the footer
				echo file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/wordpress/getfooter');

				//echo '<pre>'.print_r($_SERVER, true);
			?>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	</body>
</html>