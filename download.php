<?php
$f = $_GET ["id"];
header ( "Content-type: application/octet-stream" );
header ( "Content-Disposition: attachment; filename=\"$f\"\n" );
$fp = fopen ( "$f", "r" );
fpassthru ( $fp );
?>