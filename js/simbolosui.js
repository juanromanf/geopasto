/*
 * SimbolosUI Javascript File.
 */

var SimbolosUI = function() {
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

	function _getRecord() {
		_record = new Ext.data.Record.create([{
			name : 'id_sym'
		}, {
			name : 'detail'
		}, {
			name : 'name'
		}, {
			name : 'color'
		}, {
			name : 'outlinecolor'
		}, {
			name : 'size'
		}, {
			name : 'width'
		}]);

		return _record;
	}

	function _getColumnModel() {
		_colmodel = new Ext.grid.ColumnModel([new Ext.grid.RowNumberer(), {
			header : "Id",
			width : 40,
			hidden : true,
			sortable : true,
			dataIndex : 'id_sym'
		}, {
			header : "Detalle",
			width : 150,
			sortable : true,
			renderer : Ext.util.Format.capitalize,
			dataIndex : 'detail',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Nombre",
			width : 100,
			sortable : true,
			dataIndex : 'name',
			renderer: Ext.util.Format.uppercase,
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Color",
			width : 60,
			sortable : true,
			dataIndex : 'color',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Color de Linea",
			width : 60,
			sortable : true,
			dataIndex : 'outlinecolor',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Tama&ntilde;o",
			width : 50,
			sortable : true,
			dataIndex : 'size',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Calibre",
			width : 50,
			sortable : true,
			dataIndex : 'width',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}]);

		_colmodel.setRenderer(4, colorToCell);
		_colmodel.setRenderer(5, colorToCell);

		return _colmodel;
	}

	function colorToCell(value, metadata, record, rowIndex, colIndex, store) {
		var color = toHex(value);
		var str = '<span style="background: ' + color
				+ '; width: 15px; height: 15px;">&nbsp;&nbsp;</span>&nbsp;'
				+ value;
		return str;
	}

	function toHex(rgb) {
		var chars = '0123456789ABCDEF';
		var c = String(rgb).split(" ");
		var r = c[0];
		var g = c[1];
		var b = c[2];
		return '#'
				+ (chars[parseInt(r / 16)] + chars[parseInt(r % 16)]
						+ chars[parseInt(g / 16)] + chars[parseInt(g % 16)]
						+ chars[parseInt(b / 16)] + chars[parseInt(b % 16)]);
	}

	return {
		/*
		 * public properties, e.g. strings to translate public methods.
		 */
		init : function() {
			var xstore = new Ext.data.Store({
				autoLoad : true,
				reader : new Ext.data.JsonReader({}, _getRecord()),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'Simbolos.getAll',
					returnvalue : true,
					args : [true]
				}
			});

			_grid = new Ext.grid.EditorGridPanel({
				id : 'symbols-grid',
				clicksToEdit : 2,
				border : false,
				store : xstore,
				loadMask : true,
				autoScroll : true,
				height : _getContainer().getEl().getHeight() - 27,
				selModel : new Ext.grid.RowSelectionModel(),
				cm : _getColumnModel(),
				trackMouseOver : true,
				viewConfig : {
					forceFit : true,
					emptyText : 'No hay registros...'
				},
				listeners : {
					afteredit : function(obj) {

						xajax_AppHome.exec({
							action : 'Simbolos.updateSym',
							enableajax : true,
							args : [[{
								key : 'id_sym',
								value : obj.record.get('id_sym')
							}, {
								key : obj.field,
								value : obj.value
							}]]
						});
					}
				},
				bbar : [],
				plugins : [new Ext.ux.grid.Search({
					searchText : 'Filtro',
					mode : 'local',
					iconCls : 'icon-16-edit-find',
					dateFormat : 'Y-m-d',
					minLength : 1
				})]

			});

			var tb = new Ext.Toolbar({
				items : [{
					text : 'Nuevo simbolo',
					tooltip : 'Adicionar un simbolo.',
					iconCls : 'icon-16-list-add',
					handler : function() {
						SimbolosUI.addSymbol();
					}
				}, {
					text : 'Eliminar simbolo',
					tooltip : 'Eliminar un simbolo.',
					iconCls : 'icon-16-list-remove',
					handler : function() {
						SimbolosUI.deleteSymbol();
					}
				}, '-', {
					text : 'Cerrar',
					handler : function() {
						SimbolosUI.closeTab();
					},
					tooltip : 'Cerrar esta pesta&ntilde;a.',
					iconCls : 'icon-16-dialog-close'
				}]
			});

			_getContainer().add(tb);
			_getContainer().add(_grid);
			_getContainer().doLayout();
		}, // init end

		addSymbol : function() {
			xajax_AppHome.exec({
				action : 'Simbolos.addSym',
				enableajax : true,
				args : [[{
					key : 'detail',
					value : ''
				}, {
					key : 'name',
					value : ''
				}, {
					key : 'color',
					value : ''
				}, {
					key : 'outlinecolor',
					value : '0 0 0'
				}, {
					key : 'size',
					value : 5
				}, {
					key : 'width',
					value : 1
				}]]
			});
		},

		deleteSymbol : function() {
			var g = Ext.getCmp('symbols-grid');
			var record = g.getSelectionModel().getSelected();
			Ext.MessageBox.confirm('Eliminar',
					'Seguro desea eliminar el simbolo "' + record.get('name')
							+ '" ?', function(btn) {
						if (btn == 'yes') {
							xajax_AppHome.exec({
								action : 'Simbolos.deleteSym',
								enableajax : true,
								args : [record.get('id_sym')]
							});
						}
					});
		},

		reloadGrid : function() {
			var s = Ext.getCmp('symbols-grid').getStore();
			s.reload();
		},

		getRecord : function() {
			return _getRecord();
		},

		closeTab : function() {
			_close();
		}

	}; // return end

}();