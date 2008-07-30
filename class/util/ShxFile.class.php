<?php

class ShxFile extends File {
	private $_numRecords;
	private $_shxData;

	public function __construct($fileName, $mode = 'r') {
		$this->_numRecords = 0;
		parent::__construct($fileName, $mode);
		$this->Seek(100);
	}

	public function loadData() {

		$this->_shxData[] = array('offset'=> File::Unpack('N', $this->Read()), 'length'=> File::Unpack('N', $this->Read()));
		if ( ! $this->isEndOfFile() ) {
			$this->_numRecords++;
			$this->loadData();
		}
	}

	public function getNumRecords() {
		return $this->_numRecords;
	}

	public function getSHXData() {
		return $this->_shxData;
	}
}
?>