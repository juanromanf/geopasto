<?php

class ShpFile {
	private $_fileCode;
	private $_fileName;
	private $_fileLength;
	private $_version;
	private $_shapeType;
	private $_boundingBox	= array('Xmin'=> 0, 'Xmax'=> 0, 'Ymin'=> 0, 'Ymax'=> 0);
	private $_records		= array();
	private $_numrecords	= 0;

	/**
	 * SHPfile Object
	 *
	 * @var File
	 */
	private $_shpFile;


	/**
	 * DBFfile Object
	 *
	 * @var DbfFile
	 */
	private $_dbfFile;

	/**
	 * SHAPE TYPES
	 */
	const SHP_NULL			= 0;
	const SHP_POINT			= 1;
	const SHP_POLYLINE		= 3;
	const SHP_POLYGON		= 5;
	const SHP_MULTIPOINT	= 8;
	const SHP_POINTZ		= 11;
	const SHP_POLYLINEZ		= 13;
	const SHP_POLYGONZ		= 15;
	const SHP_MULTIPOINTZ	= 18;
	const SHP_POINTM		= 21;
	const SHP_POLYLINEM		= 23;
	const SHP_POLYGONM		= 25;
	const SHP_MULTIPOINTM	= 28;
	const SHP_MULTIPATCH	= 31;

	public function __construct($shp_type = 0, $file_name = '') {
		if ( $file_name == '' ) {
			$this->_shapeType = $shp_type;

		} else {
			/**
			 *	Read from existing file.
			 */
			$ext = strtolower( substr($file_name, strlen($file_name) - 4) );

			$arrayValidExt = array('.shp', '.shx', '.dbf');

			if ( in_array($ext, $arrayValidExt) ) {
				$file_name = str_replace( $ext, '.*', $file_name);
				$this->_fileName = $file_name;
				$this->_readMainFileHeader();

			} else {
				throw new Exception('Tipo de archivo invalido o no tiene los permisos necesarios...');
			}
		}
	}

	public function CloseAll() {
		if ( $this->_shpFile ) $this->_shpFile->Close();
		if ( $this->_dbfFile ) $this->_dbfFile->Close();
	}

	private function _readMainFileHeader() {
		$shp_file = str_replace('.*', '.shp', $this->_fileName);
		$dbf_file = str_replace('.*', '.dbf', $this->_fileName);

		$this->_dbfFile = new DbfFile($dbf_file);
		$this->_numrecords = $this->_dbfFile->getNumRecords();

		$this->_shpFile = new File($shp_file);
		$this->_fileCode = File::Unpack('N', $this->_shpFile->Read());
		$this->_shpFile->Seek(24);

		$this->_fileLength	= File::Unpack('N', $this->_shpFile->Read());
		$this->_version		= File::Unpack('V', $this->_shpFile->Read());
		$this->_shapeType	= File::Unpack('V', $this->_shpFile->Read());

		$this->_boundingBox['Xmin'] = File::Unpack('d', $this->_shpFile->Read(8));
		$this->_boundingBox['Ymin'] = File::Unpack('d', $this->_shpFile->Read(8));
		$this->_boundingBox['Xmax'] = File::Unpack('d', $this->_shpFile->Read(8));
		$this->_boundingBox['Ymax'] = File::Unpack('d', $this->_shpFile->Read(8));

		$this->getSHPFile()->Seek(100);
	}

	public function fetchRecord() {

		if ( !$this->getSHPFile()->isEndOfFile() ) {
			$shp_record = new ShpRecord(0, $this);
			$shp_record->loadData();
			return $shp_record;

		} else {
			return null;
		}
	}

	public function loadRecords() {

		$shp_record = new ShpRecord(0, $this);
		$shp_record->loadData();

		if ( ! $this->getSHPFile()->isEndOfFile() ) {
			$this->_records[] = $shp_record;
			$this->loadRecords();
		}
	}

	public function getPostGisType() {
		switch ($this->_shapeType) {
			case ShpFile::SHP_NULL : 		$str = 'NULL'; break;

			/**
			 * 	POINTS.
			 */
			case ShpFile::SHP_POINT  :
			case ShpFile::SHP_POINTM :
			case ShpFile::SHP_POINTZ :
				$str = "POINT";
				break;

			/**
			 * 	MULTIPOINTS.
			 */
			case ShpFile::SHP_MULTIPOINT  :
			case ShpFile::SHP_MULTIPOINTM :
			case ShpFile::SHP_MULTIPOINTZ :
				$str = "MULTIPOINT";
				break;

				/**
			 * 	POLYLINES.
			 */
			case ShpFile::SHP_POLYLINE  :
			case ShpFile::SHP_POLYLINEM :
			case ShpFile::SHP_POLYLINEZ :
				$str = "MULTILINESTRING";
				break;

			/**
			 * 	POLYGONS.
			 */
			case ShpFile::SHP_POLYGON   :
			case ShpFile::SHP_POLYGONM  :
			case ShpFile::SHP_POLYGONZ  :
				$str = "POLYGON";
				break;
		}

		return $str;
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

	public function getNumRecords() {
		return $this->_numrecords;
	}

	public function getFileCode() {
		return $this->_fileCode;
	}

	public function getFileName() {
		return $this->_fileName;
	}

	public function getFileLength() {
		return $this->_fileLength;
	}

	public function getVersion() {
		return $this->_version;
	}

	public function getShapeType() {
		return $this->_shapeType;
	}

	public function getBoundingBox() {
		return $this->_boundingBox;
	}

	public function getRecords() {
		return $this->_records;
	}

	/**
	 * Return associated File Object.
	 *
	 * @return File
	 */
	public function getSHPFile() {
		return $this->_shpFile;
	}

	/**
	 * Return associated DbfFile Object.
	 *
	 * @return DbfFile
	 */
	public function getDBFFile() {
		return $this->_dbfFile;
	}
}
?>