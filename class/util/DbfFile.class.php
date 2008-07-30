<?php

class DbfFile {
	private $_handle	= null;
	private $_filename	= null;
	private $_header;
	private $_numrecords;

	public function __construct($file_name) {
		$this->_filename = $file_name;
		$this->_loadHeader();
	}

	private function _loadHeader($mode = 0) {
		$this->_handle = dbase_open($this->_filename, $mode);

		if ($this->_handle == false) {
			throw new Exception("Error al intentar abrir el archivo: ". $this->_filename);

		} else {
			$this->_header = dbase_get_header_info($this->_handle);
			$this->_numrecords = dbase_numrecords($this->_handle);
		}
	}

	public function getRecord($record_number) {
		$record = dbase_get_record_with_names($this->_handle, $record_number);
		return $record;
	}

	public function Close() {
		if ( $this->_handle ) {
			dbase_close($this->_handle);
			$this->_handle = null;
		}
	}

	public function getHeader() {
		return $this->_header;
	}

	public function getNumRecords() {
		return $this->_numrecords;
	}
}
?>