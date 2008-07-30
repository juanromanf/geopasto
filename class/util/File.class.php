<?php

class File {
	private $_fileName;
	private $_fileLength;
	private $_handle;

	const FOR_READ			= 'r';
	const FOR_READ_WRITE	= 'r+';
	const FOR_APPEND		= 'a+';

	public function __construct($fileName, $mode = 'r') {
		$this->_fileName = trim($fileName);
		$this->_handle = fopen($this->_fileName, $mode);

		if ( $this->_handle == false ) {
			throw new Exception('Error al abrir el archivo: '. $this->_fileName);
		}
	}

	public function getFileName() {
		return $this->_fileName;
	}

	public function getHandle() {
		return $this->_handle;
	}

	public function Close() {
		fclose($this->_handle);
	}

	public function Seek($tobyte, $mode = SEEK_SET) {
		fseek($this->_handle, $tobyte, $mode);
	}

	public function Rewind() {
		rewind($this->_handle);
	}

	public function Read($length = 4) {
		return fread($this->_handle, $length);
	}

	public static function Unpack($type, $data) {
		$tmp = @unpack($type, $data);
    	return @current($tmp);
	}

	public function isEndOfFile() {
		return feof($this->_handle);
	}
}

?>