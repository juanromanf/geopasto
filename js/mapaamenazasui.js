/*
 * PrediosUI Javascript File.
 */

var MapaAmenazasUI = function() {
	/*
	 * do NOT access DOM from here; elements don't exist yet.
	 * 
	 * private variables. private functions
	 */

	var _container = null;

	function _getContainer() {
		_container = Ext.getCmp('center-panel').getActiveTab();
		return _container;
	}

	function _close() {
		Ext.getCmp('center-panel').remove(_getContainer(), true);
	}

	return {
		/*
		 * public properties, e.g. strings to translate public methods.
		 */

		init : function() {

			var p = new Ext.MapPanel({
				id : 'amenazas-panel',
				mapname : 'amenazas',
				mapfile : './map/amenazas.map',
				classUI : 'MapaAmenazasUI'
			});

			_getContainer().add(p);
			_getContainer().doLayout();

		}, // init end

		closeTab : function() {
			_close();
		}

	}; // return end

}();