<?php

class Simbolos extends AppActiveRecord {
	public $_table = 'symbols.simbolos';
	
	public function getKey() {
		return $this->key;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getColor() {
		return $this->color;
	}
	
	public function getOutLineColor() {
		return $this->outlinecolor;
	}
	
	public function getSize() {
		return $this->size;
	}

}
?>