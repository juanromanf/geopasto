/*
 * ConfigUI Javascript File
 */

var ConfigUI = function() {

	function _getContainer() {
		var container = Ext.getCmp('center-panel').getActiveTab();
		return container;
	}

	function _close() {
		Ext.getCmp('center-panel').remove(_getContainer(), true);
	}

	return {
		init : function() {
			
			var r = new Ext.data.Record.create([{
				name : 'id'
			}, {
				name : 'key'
			}, {
				name : 'text'
			}, {
				name : 'value'
			}]);

			var ds = new Ext.data.Store({
				autoLoad : true,
				reader : new Ext.data.JsonReader({}, r),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'Config.getAllValues',
					returnvalue : true,
					args : [true]
				}
			});

			var cm = new Ext.grid.ColumnModel([{
				header : "Propiedad",
				dataIndex : 'text',
				align : 'right',
				menuDisabled : true,
				sortable : true,
				width : 230
			}, {
				header : "Valor",
				dataIndex : 'value',
				menuDisabled : true,
				sortable : false,
				width : 120,
				editor : new Ext.grid.GridEditor(new Ext.form.TextField())
			}]);

			var grid = new Ext.grid.EditorGridPanel({
				clicksToEdit : 2,
				ds : ds,
				cm : cm,
				height : _getContainer().getEl().getHeight() - 27,
				// selModel : new Ext.grid.RowSelectionModel(),
				frame : true,
				loadMask : true,
				autoScroll : true,
				border : false,
				trackMouseOver : true,
				listeners : {
					afteredit : function(obj) {
						xajax_AppHome.exec({
							action : 'Config.setValue',
							args : [obj.record.get('key'), obj.value]
						});
					}
				}
			});

			var tb = new Ext.Toolbar({
				items : ['Theme: ', ConfigUI.getThemesCombo(), '-', {
					text : 'Cerrar',
					handler : function() {
						ConfigUI.closeTab();
					},
					tooltip : 'Cerrar esta pesta&ntilde;a.',
					iconCls : 'icon-16-dialog-close'
				}]
			});
			
			

			_getContainer().add(tb);
			_getContainer().add(grid);
			_getContainer().doLayout();
			
		},

		closeTab : function() {
			_close();
		},

		getThemesCombo : function() {
			
			/*
			 * Store para Themes disponibles.
			 */
			
			var ds = new Ext.data.SimpleStore({
				fields : ['file', 'text'],
				data : [['ext-all.css', 'Default'],
						['xtheme-black.css', 'Black'],
						['xtheme-darkgray.css', 'Dark Gray'],
						['xtheme-gray.css', 'Gray'],
						['xtheme-olive.css', 'Olive'],
						['xtheme-purple.css', 'Purple'],
						['xtheme-slate.css', 'Slate'],
						['xtheme-slickness.css', 'Slickness']]
			});

			var cmb = new Ext.form.ComboBox({
				id : 'theme-combo',
				store : ds,
				displayField : 'text',
				valueField : 'file',
				mode : 'local',
				triggerAction : 'all',
				emptyText : 'Seleccione...',
				forceSelection : true,
				editable : false
			});

			cmb.on('select', function(combo, record, index) {
				var theme = record.get('file');
				var file = 'include/ext/resources/css/' + theme;
				Ext.util.CSS.swapStyleSheet('theme-css', file);
				Ext.util.CSS.refreshCache();

				xajax_AppHome.exec({
					action : 'Config.setValue',
					args : ['theme', theme]
				});
			});

			return cmb;
		}
	}; // end return
}();