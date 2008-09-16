/*
 * ConvencionesUI Javascript File.
 */

var ConvencionesUI = function() {
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
			name : 'gid'
		}, {
			name : 'map'
		}, {
			name : 'layer'
		}, {
			name : 'keyvalue'
		}, {
			name : 'id_sym'
		}, {
			name : 'operator'
		}, {
			name : 'display'
		}]);

		return _record;
	}

	function _getColumnModel() {

		var ds = new Ext.data.Store({
			// autoLoad : true,
			reader : new Ext.data.JsonReader({}, SimbolosUI.getRecord()),
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

		var cmbOp = new Ext.form.ComboBox({
			triggerAction : 'all',
			mode : 'local',
			listClass : 'x-combo-list-small',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : [['=', 'Igual ='], ['>', 'Mayor >'], ['<', 'Menor <'],
						['ne', 'Diferente !=']]
			}),
			valueField : 'value',
			displayField : 'text'
		});

		var cmbSym = new Ext.form.ComboBox({
			store : ds,
			listClass : 'x-combo-list-small',
			displayField : 'detail',
			valueField : 'id_sym',
			mode : Ext.isIE ? 'local' : 'remote',
			triggerAction : 'all',
			emptyText : '...',
			forceSelection : true,
			editable : false
		});

		_colmodel = new Ext.grid.ColumnModel([new Ext.grid.RowNumberer(), {
			header : "Mapa",
			width : 100,
			sortable : true,
			renderer : Ext.util.Format.capitalize,
			dataIndex : 'map',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Capa",
			width : 100,
			sortable : true,
			dataIndex : 'layer',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Condicion",
			width : 40,
			sortable : true,
			dataIndex : 'operator',
			editor : cmbOp,
			renderer : Ext.grid.comboBoxRenderer(cmbOp)
		}, {
			header : "Valor",
			width : 40,
			sortable : true,
			dataIndex : 'keyvalue',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Simbolo",
			width : 80,
			sortable : true,
			dataIndex : 'id_sym',
			editor : cmbSym,
			renderer : Ext.grid.comboBoxRenderer(cmbSym)
		}, {
			header : "Etiqueta",
			width : 100,
			sortable : true,
			dataIndex : 'display',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}]);

		ds.load();

		return _colmodel;
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
					action : 'Convenciones.getAll',
					returnvalue : true,
					args : ['%', '%', true]
				}
			});

			_grid = new Ext.grid.EditorGridPanel({
				id : 'conventions-grid',
				clicksToEdit : 2,
				border : false,
				title : ' ',
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
							action : 'Convenciones.modify',
							enableajax : true,
							args : [[{
								key : 'gid',
								value : obj.record.get('gid')
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
					text : 'Nueva convencion',
					tooltip : 'Adicionar una convencion a un mapa.',
					iconCls : 'icon-16-list-add',
					handler : function() {
						ConvencionesUI.add();
					}
				}, {
					text : 'Eliminar convencion',
					tooltip : 'Eliminar un simbolo.',
					iconCls : 'icon-16-list-remove',
					handler : function() {
						ConvencionesUI.remove();
					}
				}, '-', {
					text : 'Cerrar',
					handler : function() {
						ConvencionesUI.closeTab();
					},
					tooltip : 'Cerrar esta pesta&ntilde;a.',
					iconCls : 'icon-16-dialog-close'
				}]
			});

			_getContainer().add(tb);
			_getContainer().add(_grid);
			_getContainer().doLayout();
		}, // init end

		add : function() {
			xajax_AppHome.exec({
				action : 'Convenciones.add',
				enableajax : true,
				args : [[{
					key : 'map',
					value : ''
				}, {
					key : 'layer',
					value : ''
				}, {
					key : 'keyvalue',
					value : ''
				}, {
					key : 'operator',
					value : '='
				}, {
					key : 'id_sym',
					value : 1
				}, {
					key : 'display',
					value : ''
				}]]
			});
		},

		remove : function() {
			var g = Ext.getCmp('conventions-grid');
			var record = g.getSelectionModel().getSelected();
			Ext.MessageBox.confirm('Eliminar',
					'Seguro desea eliminar esta convencion ?', function(btn) {
						if (btn == 'yes') {
							xajax_AppHome.exec({
								action : 'Convencion.remove',
								enableajax : true,
								args : [record.get('gid')]
							});
						}
					});
		},

		reloadGrid : function() {
			var s = Ext.getCmp('conventions-grid').getStore();
			s.reload();
		},

		closeTab : function() {
			_close();
		}

	}; // return end

}();