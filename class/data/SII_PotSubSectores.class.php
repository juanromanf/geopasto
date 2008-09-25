<?php

class SII_PotSubSectores extends AppActiveRecord {
	public $_table = 'public.p_pot_subsectores';
	
	public function getNombreSubsector() {
		return $this->subsector;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return SII_PotSectores
	 */
	public function getSector() {
		$sector = new SII_PotSectores();
		$sector->Load("codsector = '". $this->codsector."'");
		return $sector;
	}
}
?>