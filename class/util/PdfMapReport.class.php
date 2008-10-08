<?php

class PdfMapReport extends FPDF {
	private $map;
	
	public function __construct(msMap $map) {
		parent::FPDF ();
		
		$this->setMap ( $map );
		$this->AddPage ();
		$this->drawFrame ();
		$this->addImages ();
	}
	
	public function RoundedRect($x, $y, $w, $h, $r, $style = '') {
		$k = $this->k;
		$hp = $this->h;
		if ($style == 'F')
			$op = 'f';
		elseif ($style == 'FD' or $style == 'DF')
			$op = 'B';
		else
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
	
	private function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		$h = $this->h;
		$this->_out ( sprintf ( '%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k ) );
	}
	
	public function setMap(msMap $mapObj) {
		$this->map = $mapObj;
	}
	
	/**
	 * Retorna la instancia de la clase msMap utilizada para
	 * el reporte.
	 *
	 * @return msMap
	 */
	public function getMapObj() {
		return $this->map;
	}
	
	private function drawFrame() {
		// build frame
		$margin = 10;
		$this->SetLineWidth ( .5 );
		
		// margen
		$this->Rect ( $margin, $margin, $this->w - ($margin * 2), $this->h - ($margin * 2) );
		// titulo
		$this->Rect ( $margin, $margin, $this->w - ($margin * 2), 20 );
		
		// mapa
		$this->Rect ( $margin, 20 + $margin, $this->w - ($margin * 2), 142 );
		
		$this->SetFont ( 'Arial', 'B', 13 );
		//Move to the right
		$this->Cell ( 80 );
		
		//Title
		$this->Cell ( 30, 15, 'SISTEMA DE INFORMACION GEOGRAFICA', 0, 0, 'C' );
		$this->Ln ( 5 );
		$this->Cell ( 80 );
		$this->Cell ( 30, 15, 'ALCALDIA DE PASTO', 0, 0, 'C' );
		
		$this->Image ( '../img/logo4pdf.jpg', 13, 12 );
	}
	
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
	
	private function addImages() {
		
		$map = $this->getMapObj ();
		
		$image_map = $this->parseImage ( $map->drawMap () );
		$image_legend = $this->parseImage ( $map->drawLegend () );
		
		$w = 530;
		$h = $w * .75;
		$this->Image ( $image_map ['url'], 11, 31, $w / $this->k, $h / $this->k );
		
		$w = $image_legend ['w'];
		$h = ($image_legend ['h'] > 300) ? 300 : $image_legend ['h'];
		$this->Image ( $image_legend ['url'], 15, 175, $w / $this->k, $h / $this->k );
	}
}
?>