<?php 
$curl = curl_init("http://www.kortingscode.nl/alle-winkels");
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "REFRESH");
curl_exec($curl);
?>