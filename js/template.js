/*
 * CLASSNAME Javascript File.
 */

var CLASSNAME = function() {
	/*
	 * do NOT access DOM from here; elements don't exist yet.
	 * 
	 * private variables.
	 * private functions
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
		 * public properties, e.g. strings to translate
		 * public methods.
		 */
		
		init : function () {
			var myTab = _getContainer();
			myTab.getEl().mask('Cargando, por favor espere...', 'x-mask-loading');
			
			myTab.add();
			myTab.doLayout();
			myTab.getEl().unmask();
			
		}, // init end

		closeTab : function() {
			_close();
		}
		
	}; // return end
	
}();