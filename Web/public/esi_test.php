<?php echo '<pre>'.print_r($_SERVER, true).'</pre>';  ?>
<html>
<head>
  <title>ESI Test</title>
</head>
  <body>
    <h1>ESI test geeft u de tijd vanuit een ESI include</h1>
    <p>De tijd is ESI is <esi:include src="/public/time.php" /> en via php include <?php require_once 'time.php' ?></p>
  </body>
</html>