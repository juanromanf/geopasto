<?php

class PostGIS {

	private $_conn = null;

	public function __construct() {
		$this->Connect();
	}

	public function Connect() {
		$connStr = "host=" . AppSQL::HOST . " dbname=" . AppSQL::DATABASE . " user=". AppSQL::USER . " password=" . AppSQL::PASSWORD;
		$this->_conn = pg_connect($connStr);
	}

	public function Execute($query){
		if ($this->_conn != null) {
			pg_exec($this->_conn, $query);
		}
	}

	public function Close() {
		pg_close($this->_conn);
	}

	public function AddGeometryColumn($schema, $tablename, $col_name, $srid, $type, $dimension) {
		$query = "SELECT AddGeometryColumn('$schema','$tablename','$col_name', $srid,'$type','$dimension')";
		$this->Execute($query);
	}
}
?>