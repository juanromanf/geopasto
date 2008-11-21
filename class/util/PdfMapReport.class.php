<?php
/**
 * 
 * Clase responsable de la contruccion de los reportes PDF 
 * para los mapa. 
 * 
 * @package util
 */
class PdfMapReport extends FPDF {
	private $map;
	/**
	 * 
	 * Constructor de la Clase.
	 *
	 * @param msMap $map
	 */
	public function __construct(msMap $map) {
		parent::FPDF ( 'l', 'mm', 'letter' );
		
		$this->setMap ( $map );
		$this->AddPage ();
		$this->drawFrame ();
		$this->addImages ();
	}
	
	/**
	 * Dibuja un rectangulo redondeado en el documento con los valores proporcionados.
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $w
	 * @param integer $h
	 * @param integer $r
	 * @param string $style
	 */
	public function RoundedRect($x, $y, $w, $h, $r, $style = '') {
		$k = $this->k;
		$hp = $this->h;
		if ($style == 'F')
			$op = 'f'; elseif ($style == 'FD' or $style == 'DF')
			$op = 'B'; else
			$op = 'S';
		$MyArc = 4 / 3 * (sqrt ( 2 ) - 1);
		$this->_out ( sprintf ( '%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k ) );
		$xc = $x + $w - $r;
		$yc = $y + $r;
		$this->_out ( sprintf ( '%.2F %.2F l', $xc * $k, ($hp - $y) * $k ) );
		
		$this->_Arc ( $xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc );
		$xc = $x + $w - $r;
		$yc = $y + $h - $r;
		$this->_out ( sprintf ( '%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k ) );
		$this->_Arc ( $xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r );
		$xc = $x + $r;
		$yc = $y + $h - $r;
		$this->_out ( sprintf ( '%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k ) );
		$this->_Arc ( $xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc );
		$xc = $x + $r;
		$yc = $y + $r;
		$this->_out ( sprintf ( '%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k ) );
		$this->_Arc ( $xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r );
		$this->_out ( $op );
	}
	
	/**
	 * Dibuja los arcos para el rectangulo redondeado.
	 *
	 * @param Integer $x1
	 * @param Integer $y1
	 * @param Integer $x2
	 * @param Integer $y2
	 * @param Integer $x3
	 * @param Integer $y3
	 */
	private function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		$h = $this->h;
		$this->_out ( sprintf ( '%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k ) );
	}
	
	/**
	 * Agrega la instancia de la clase msMAp utilizada para el reporte.
	 *
	 * @param msMap $mapObj
	 */
	public function setMap(msMap $mapObj) {
		$this->map = $mapObj;
	}
	
	/**
	 * Retorna la instancia de la clase msMap utilizada para el reporte.
	 *
	 * @return msMap
	 */
	public function getMapObj() {
		return $this->map;
	}
	
	/**
	 * Crea el formato del reporte.
	 *
	 */
	private function drawFrame() {
		// build frame
		$margin = 5;
		$this->SetLineWidth ( .5 );
		
		// Marco del logo
		$this->RoundedRect ( $this->w - $margin - 55, $margin, 55, 55, 3 );
		
		// Marco del mapa de referencia
		$this->RoundedRect ( $this->w - $margin - 55, 62, 55, 103, 3 );
		
		// Marco del mapa
		$this->RoundedRect ( $margin, $margin, $this->w - ($margin * 2) - 57, 160, 1.5 );
		
		// Marco obsevaciones
		$this->RoundedRect ( $margin, 167, $this->w - ($margin * 2), 45, 1.5 );
		
		$this->SetFont ( 'Arial', 'B', 10 );
		
		//Title
		$this->Cell ( $this->w - $margin - 55 );
		$this->Cell ( 35, 6, 'SISTEMA DE INFORMACIÓN', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->Cell ( $this->w - $margin - 55 );
		$this->Cell ( 35, 6, 'GEOGRÁFICA', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->Cell ( $this->w - $margin - 55 );
		$this->Cell ( 35, 60, 'ALCALDIA DE PASTO', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->Cell ( $this->w - $margin - 55 );
		$this->Cell ( 35, 90, 'UBICACIÓN', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->Cell ( $this->w - $margin - 55 );
		$this->Cell ( 35, 90, 'GENERAL', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->SetXY ( 7, 170 );
		$this->Cell ( 35, 10, 'OBSERVACIONES', 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$this->Image ( '../img/logo4pdf.jpg', $this->w - 40, 25 );
	}
	
	/**
	 * Convierte el formato de la imagen del mapa PNG a GIF.
	 *
	 * @param string $img_path
	 * @return array
	 */
	private function parseImage($img_path) {
		$tmp_path = $this->getMapObj ()->getWebImagePath ();
		
		$image_url = $tmp_path . basename ( $img_path );
		$handle = imagecreatefrompng ( $image_url );
		$save_to = $tmp_path . basename ( $img_path, '.png' ) . '.gif';
		imagegif ( $handle, $save_to );
		$w = imagesx ( $handle );
		$h = imagesy ( $handle );
		
		return (array ('url' => $save_to, 'w' => $w, 'h' => $h ));
	}
	
	/**
	 * Inserta las imagenes que van en el documento.
	 */
	private function addImages() {
		
		$map = $this->getMapObj ();
		
		$image_map = $this->parseImage ( $map->drawMap () );
		$image_legend = $this->parseImage ( $map->drawLegend () );
		$image_reference = $this->parseImage ( $map->drawReferenceMap () );
		
		$this->Cell ( $this->w - 60 );
		$this->SetXY ( 230, 150 );
		$this->Cell ( 35, 10, 'ESCALA 1 : ' . round ( $map->getMapScale () ), 0, 0, 'C' );
		$this->Ln ( 5 );
		
		$w = 595;
		$h = $w * .75;
		$this->Image ( $image_map ['url'], 6, 6, $w / $this->k, $h / $this->k );
		
		$w = $image_legend ['w'];
		$h = ($image_legend ['h'] > 300) ? 300 : $image_legend ['h'];
		$this->Image ( $image_reference ['url'], $this->w - 54, 90 );
	}
}
?>