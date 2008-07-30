<?php

class ShpRecord {
	/**
	 * SHP object
	 *
	 * @var SHPFile
	 */
	private $_shpObj;
	private $_recordNumber;
	private $_contentLength;
	private $_shapeType;

	private $_shpData = array();
	private $_dbfData = array();

	public function __construct($shapeType = 0, $shpObj = null) {
		$this->_shapeType = $shapeType;
		$this->_shpObj = $shpObj;
	}

	private function _loadRecordHeader() {

		/* @var $shpFile File */
		$shpFile = $this->_shpObj->getSHPFile();
		$this->_recordNumber  = File::Unpack('N', $shpFile->Read());
		$this->_contentLength = File::Unpack('N', $shpFile->Read());
		$this->_shapeType	  = File::Unpack('V', $shpFile->Read());
	}

	public function loadData() {

		$this->_loadRecordHeader();
		$this->_dbfData = $this->_shpObj->getDBFFile()->getRecord($this->_recordNumber);

		switch ($this->_shapeType) {
			/**
			 * 	Common Shapes.
			 */
			case ShpFile::SHP_NULL :
				$this->_loadNullShape();
				break;

			case ShpFile::SHP_POINT :
				$this->_loadPointShape();
				break;

			case ShpFile::SHP_MULTIPOINT :
				$this->_loadMultiPointShape();
				break;

			case ShpFile::SHP_POLYLINE :
				$this->_loadPolyLineShape();
				break;

			case ShpFile::SHP_POLYGON :
				$this->_loadPolygonShape();
				break;

				/**
			 *	M Shapes.
			 */
			case ShpFile::SHP_POINTM :
				$this->_loadPointMShape();
				break;

			case ShpFile::SHP_MULTIPOINTM :
				$this->_loadMultiPointMShape();
				break;

			case ShpFile::SHP_POLYLINEM :
				$this->_loadPolyLineMShape();;
				break;

			case ShpFile::SHP_POLYGONM :
				$this->_loadPolygonMShape();
				break;

				/**
			 * 	Z Shapes.
			 */
			case ShpFile::SHP_POINTZ :
				$this->_loadPointZShape();
				break;

			case ShpFile::SHP_MULTIPOINTZ :
				$this->_loadMultiPointZShape();
				break;

			case ShpFile::SHP_POLYLINEZ :
				$this->_loadPolyLineZShape();
				break;

			case ShpFile::SHP_POLYGONZ :
				$this->_loadPolygonZShape();
				break;

			case ShpFile::SHP_MULTIPATCH :
				$this->_loadMultiPatchShape();
				break;
		}
	}

	private function _loadPoint() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();
		$data = array();
		$data["x"] = File::Unpack('d', $shp_file->Read(8));
		$data["y"] = File::Unpack('d', $shp_file->Read(8));
		return $data;
	}

	private function _loadNullShape() {
		$this->_shpData = array();
	}

	private function _loadPointShape() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['points'] = $this->_loadPoint();
	}

	private function _loadMultiPointShape() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['boundingbox']['Xmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Xmax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['numpoints'] = File::Unpack('V', $shp_file->Read());

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['points'][$i] = $this->_loadPoint();
		}
	}

	private function _loadPolyLineShape() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['boundingbox']['Xmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Xmax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['numparts']  = File::Unpack('V', $shp_file->Read());
		$this->_shpData['numpoints'] = File::Unpack('V', $shp_file->Read());

		for ($i = 0; $i < $this->_shpData['numparts']; $i++) {
			$this->_shpData['parts'][$i] = File::Unpack('V', $shp_file->Read());
		}

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['points'][$i] = $this->_loadPoint();
		}

	}

	private function _loadPolygonShape() {
		$this->_loadPolyLineShape();
	}

	private function _loadPointMShape() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['X'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Y'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['M'] = File::Unpack('d', $shp_file->Read(8));
	}

	private function _loadMultiPointMShape() {

		$this->_loadMultiPointShape();

		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}
	}

	private function _loadPolyLineMShape() {

		$this->_loadPolyLineShape();

		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}
	}

	private function _loadPolygonMShape() {
		$this->_loadPolygonShape();

		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}
	}

	private function _loadPointZShape() {
		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shapeType = File::Unpack('V', $shp_file->Read());
		$this->_shpData['X'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Y'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Z'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['M'] = File::Unpack('d', $shp_file->Read(8));
	}

	private function _loadMultiPointZShape() {
		$this->_loadMultiPointShape();

		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['Zmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Zmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Zarray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}
	}

	private function _loadPolyLineZShape() {
		$this->_loadPolyLineShape();

		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['Zmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Zmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Zarray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}

	}

	public function _loadPolygonZShape() {
		$this->_loadPolyLineZShape();
	}

	public function _loadMultiPatchShape() {

		/* @var $shp_file File */
		$shp_file = $this->_shpObj->getSHPFile();

		$this->_shpData['boundingbox']['Xmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Xmax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['boundingbox']['Ymax'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['numparts']  = File::Unpack('V', $shp_file->Read());
		$this->_shpData['numpoints'] = File::Unpack('V', $shp_file->Read());

		for ($i = 0; $i < $this->_shpData['numparts']; $i++) {
			$this->_shpData['parts'][$i] = File::Unpack('V', $shp_file->Read());
		}

		for ($i = 0; $i < $this->_shpData['numparts']; $i++) {
			$this->_shpData['parttypes'][$i] = File::Unpack('V', $shp_file->Read());
		}

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['points'][$i] = $this->_loadPoint();
		}

		$this->_shpData['Zmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Zmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Zarray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}

		$this->_shpData['Mmin'] = File::Unpack('d', $shp_file->Read(8));
		$this->_shpData['Mmax'] = File::Unpack('d', $shp_file->Read(8));

		for ($i = 0; $i < $this->_shpData['numpoints']; $i++) {
			$this->_shpData['Marray'][$i] = File::Unpack('d', $shp_file->Read(8));
		}
	}


	public function shapeTypeToSring() {
		switch ($this->_shapeType) {
			case ShpFile::SHP_NULL : 		$str = 'NULL SHAPE'; break;
			case ShpFile::SHP_POINT :		$str = 'POINT'; break;
			case ShpFile::SHP_MULTIPOINT :	$str = 'MULTIPOINT'; break;
			case ShpFile::SHP_POLYLINE : 	$str = 'POLYLINE'; break;
			case ShpFile::SHP_POLYGON : 	$str = 'POLYGON'; break;

			case ShpFile::SHP_POINTM : 		$str = 'POINTM'; break;
			case ShpFile::SHP_MULTIPOINTM :	$str = 'MULTIPOINTM'; break;
			case ShpFile::SHP_POLYLINEM : 	$str = 'POLYLINEM'; break;
			case ShpFile::SHP_POLYGONM : 	$str = 'POLYGONM'; break;

			case ShpFile::SHP_POINTZ  : 	$str = 'POINTZ'; break;
			case ShpFile::SHP_MULTIPOINTZ : $str = 'MULTIPOINTZ'; break;
			case ShpFile::SHP_POLYLINEZ : 	$str = 'POLYLINEZ'; break;
			case ShpFile::SHP_POLYGONZ : 	$str = 'POLYGONZ'; break;
			case ShpFile::SHP_MULTIPATCH : 	$str = 'MULTIPATCH'; break;
		}

		return $str;
	}

	public function toWKT() {
		switch ($this->_shapeType) {
			case ShpFile::SHP_NULL : 		$str = 'NULL'; break;

			/**
			 * 	POINTS.
			 */
			case ShpFile::SHP_POINT  :
			case ShpFile::SHP_POINTM :
			case ShpFile::SHP_POINTZ :
				$str = "POINT( ". $this->getWKTPoints() . " )";
				break;

			/**
			 * 	MULTIPOINTS.
			 */
			case ShpFile::SHP_MULTIPOINT  :
			case ShpFile::SHP_MULTIPOINTM :
			case ShpFile::SHP_MULTIPOINTZ :
				$str = "MULTIPOINT( ". $this->getWKTPoints() ." )";
				break;

				/**
			 * 	POLYLINES.
			 */
			case ShpFile::SHP_POLYLINE  :
			case ShpFile::SHP_POLYLINEM :
			case ShpFile::SHP_POLYLINEZ :
				$str = "MULTILINESTRING( ". $this->getWKTPoints() ." )";
				break;

			/**
			 * 	POLYGONS.
			 */
			case ShpFile::SHP_POLYGON   :
			case ShpFile::SHP_POLYGONM  :
			case ShpFile::SHP_POLYGONZ  :
				$str = 'POLYGON( '. $this->getWKTPoints() ." )";
				break;
		}

		return $str;
	}

	private function getWKTPoints() {
		switch ($this->_shapeType) {

			/**
			 * 	POINTS, MULTIPOINTS.
			 */
			case ShpFile::SHP_POINT  :
			case ShpFile::SHP_POINTM :
			case ShpFile::SHP_POINTZ :
			case ShpFile::SHP_MULTIPOINT  :
			case ShpFile::SHP_MULTIPOINTM :
			case ShpFile::SHP_MULTIPOINTZ :

				$str = '';
				foreach ($this->_shpData['points'] as $point) {
					$str .= $point['x'] . " ". $point['y']. ", ";
				}
				$str = substr($str, 0, strlen($str) - 2);
				break;

				/**
			 * 	POLYLINES, POLYGONS.
			 */
			case ShpFile::SHP_POLYLINE  :
			case ShpFile::SHP_POLYLINEM :
			case ShpFile::SHP_POLYLINEZ :
			case ShpFile::SHP_POLYGON   :
			case ShpFile::SHP_POLYGONM  :
			case ShpFile::SHP_POLYGONZ  :

				$str = '';

				if ( $this->_shpData['numparts'] == 1) {
					foreach ($this->_shpData['points'] as $point) {
						$str .= $point['x'] . " ". $point['y']. ", ";
					}
					$str = "(". substr($str, 0, strlen($str) - 2) . ")";

				} else {

					for ($i = 1; $i < $this->_shpData['numparts']; $i++) {
						$index0 = $this->_shpData['parts'][$i - 1];
						$index1 = $this->_shpData['parts'][$i];

						$str .= "( ";
						for ($j = $index0; $j < $index1; $j++) {
							$point = $this->_shpData['points'][$j];
							$str .= $point['x'] . " ". $point['y']. ", ";
						}
						$str = substr($str, 0, strlen($str) - 2);

						$str .= "), ";
					}
					$str = substr($str, 0, strlen($str) - 2);
				}
				break;

			case ShpFile::SHP_MULTIPATCH :
				$str = ' ... ';
				break;
		}

		return $str;
	}

	public function getPoints() {
		$str = '';

		foreach ($this->_shpData['points'] as $point) {
			$str .= $point['x'] . " ". $point['y']. ", ";
		}
		$str = substr($str, 0, strlen($str) - 2);
		return $str;
	}

	public function getRecordNumber() {
		return $this->_recordNumber;
	}

	public function getShapeType() {
		return $this->_shapeType;
	}

	public function getSHPData() {
		return $this->_shpData;
	}

	public function getDBFData() {
		return $this->_dbfData;
	}
}
?>