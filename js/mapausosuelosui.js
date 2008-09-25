/*
 * PrediosUI Javascript File.
 */

var MapaUsoSuelosUI = function() {
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
				id : 'usosuelos-panel',
				mapname : 'usosuelos',
				mapfile : './map/usosuelos.map',
				classUI : 'MapaUsoSuelosUI',
				queryFunction : MapaUsoSuelosUI.getInfo
			});

			_getContainer().add(p);
			_getContainer().doLayout();

		}, // init end

		getInfo : function(x, y) {

			var record = new Ext.data.Record.create([, {
				name : 'seccion'
			}, {
				name : 'property'
			}, {
				name : 'value'
			}]);

			var ds = new Ext.data.GroupingStore({
				autoLoad : true,
				groupField : 'seccion',
				sortInfo : {
					field : 'seccion',
					direction : "ASC"
				},
				reader : new Ext.data.JsonReader({}, record),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'MapaUsoSuelosUI.doQuery',
					returnvalue : true,
					enableajax : true,
					args : [{
						x : x,
						y : y,
						extent : xajax.$('usosuelos-ex').value
					}]
				}
			});

			var cm = new Ext.grid.ColumnModel([{
				header : "&nbsp;",
				dataIndex : 'property',
				css : 'text-align: right; font-weight: bold;',
				width : 120,
				sortable : false,
				fixed : true,
				renderer : function(val) {
					return val + ':';
				}
			}, {
				header : "&nbsp;",
				dataIndex : 'value',
				sortable : false
			}, {
				header : "&nbsp;",
				hidden : true,
				dataIndex : 'seccion',
				sortable : false
			}]);

			var grid = new Ext.grid.GridPanel({
				store : ds,
				colModel : cm,
				loadMask : true,
				enableHdMenu : false,
				view : new Ext.grid.GroupingView({
					forceFit : true,
					groupTextTpl : '{group}'
				})
			});

			if (Ext.getCmp('info-win')) {
				Ext.getCmp('info-win').close();
			}

			var win = new Ext.Window({
				id : 'info-win',
				iconCls : 'icon-16-help-contents',
				layout : 'fit',
				width : 400,
				height : 250,
				resizable : true,
				autoScroll : true,
				modal : false,
				title : 'Informaci&oacute;n',
				closeAction : 'close',
				plain : true,
				items : grid
			});

			win.show();
		},

		closeTab : function() {
			_close();
		}

	}; // return end

}();