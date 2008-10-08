<?php

require ('../app.config.php');

$map_file = $_GET ["map"];
$map = new msMap ( $map_file );

$pdf = new PdfMapReport ( $map );
$pdf->Output ( 'map.pdf', 'D' );
?>