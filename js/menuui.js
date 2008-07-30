/*
 * MenuUI Javascript File.
 */

var MenuUI = function() {
	/*
	 * do NOT access DOM from here; elements don't exist yet.
	 * 
	 * private variables. private functions
	 */
	var _colmodel = null;
	var _grid = null;
	var _record = null;
	var _container = null;

	function _getContainer() {
		_container = Ext.getCmp('center-panel').getActiveTab();
		return _container;
	}

	function _close() {
		Ext.getCmp('center-panel').remove(_getContainer(), true);
	}

	function _getMenuGrid() {
		_grid = Ext.getCmp('menu-grid');
		return _grid;
	}

	function _getMenuRecord() {
		_record = new Ext.data.Record.create([{
			name : 'id'
		}, {
			name : 'id_module'
		}, {
			name : 'text'
		}, {
			name : 'action'
		}, {
			name : 'iconcls'
		}, {
			name : 'position'
		}]);

		return _record;
	}

	function _getColumnModel() {
		_colmodel = new Ext.grid.ColumnModel([new Ext.grid.RowNumberer(), {
			header : "Id",
			width : 40,
			hidden : true,
			sortable : true,
			dataIndex : 'id'
		}, {
			header : "Texto",
			width : 100,
			sortable : true,
			renderer : Ext.util.Format.capitalize,
			dataIndex : 'text',
			editor : new Ext.form.TextField({
				allowBlank : false
			})
		}, {
			header : "Accion",
			width : 70,
			sortable : true,
			dataIndex : 'action',
			editor : new Ext.form.TextField({
				allowBlank : true
			})
		}, {
			header : "Icono",
			width : 70,
			sortable : true,
			dataIndex : 'iconcls',
			editor : new Ext.form.TextField({
				allowBlank : true
			})
		}, {
			header : "Posicion",
			width : 40,
			sortable : true,
			dataIndex : 'position',
			editor : new Ext.form.NumberField({
				allowBlank : false,
				allowNegative : false,
				maxValue : 100000
			})
		}]);

		return _colmodel;
	}

	return {
		/*
		 * public properties, e.g. strings to translate public methods.
		 */

		init : function() {

			var r = new Ext.data.Record.create([{
				name : 'id'
			}, {
				name : 'title'
			}]);

			var ds = new Ext.data.Store({
				autoLoad : true,
				reader : new Ext.data.JsonReader({}, r),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'AppModules.getAllModules',
					returnvalue : true,
					args : [true]
				}
			});

			var cmb = new Ext.form.ComboBox({
				id : 'modules-combo',
				hiddenName : 'id_module',
				store : ds,
				displayField : 'title',
				valueField : 'id',
				typeAhead : true,
				editable : false,
				allowBlank : true,
				mode : Ext.isIE ? 'local' : 'remote',
				triggerAction : 'all',
				loadingText : 'Cargando...',
				emptyText : 'Seleccione un modulo...',
				selectOnFocus : true
			});

			cmb.on('select', function(combo, record, index) {

				var s = Ext.getCmp('menu-grid').getStore();
				s.baseParams.args = [true, combo.getValue()];
				s.load();
			});

			var xstore = new Ext.data.Store({
				// autoLoad : true,
				reader : new Ext.data.JsonReader({}, _getMenuRecord()),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'AppModules.getMenus',
					returnvalue : true
				}
			});

			_grid = new Ext.grid.EditorGridPanel({
				id : 'menu-grid',
				clicksToEdit : 2,
				border : false,
				title : 'Items del menu',
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
							action : 'AppModuleMenus.updateItem',
							enableajax : true,
							args : [[{
								key : 'id',
								value : obj.record.get('id')
							}, {
								key : obj.field,
								value : obj.value
							}]]
						});
					}
				}
			});

			var tb = new Ext.Toolbar({
				items : ['Modulo: ', cmb, '-', {
					text : 'Nuevo modulo',
					tooltip : 'Adicionar un nuevo modulo.',
					iconCls : 'icon-16-list-add',
					handler : function() {
						MenuUI.addModule();
					}
				}, {
					text : 'Editar modulo',
					tooltip : 'Editar propiedades del modulo.',
					iconCls : 'icon-16-gtk-edit',
					handler : function() {
						MenuUI.editModule();
					}
				}, {
					text : 'Eliminar modulo',
					tooltip : 'Eliminar el modulo.',
					iconCls : 'icon-16-list-remove',
					handler : function() {
						MenuUI.deleteModule();
					}
				}, '-', {
					text : 'Nuevo item',
					tooltip : 'Adicionar item al modulo activo.',
					iconCls : 'icon-16-list-add',
					handler : function() {
						MenuUI.addItem();
					}
				}, {
					text : 'Eliminar item',
					tooltip : 'Eliminar el item seleccionado.',
					iconCls : 'icon-16-list-remove',
					handler : function() {
						MenuUI.deleteItem();
					}
				}, '-', {
					text : 'Cerrar',
					handler : MenuUI.closeTab,
					tooltip : 'Cerrar esta pesta&ntilde;a.',
					iconCls : 'icon-16-dialog-close'
				}]
			});

			_getContainer().add(tb);
			_getContainer().add(_grid);
			_getContainer().doLayout();
		}, // init end

		closeTab : function() {
			_close();
		},

		reloadCombo : function() {
			var cmb = Ext.getCmp('modules-combo');
			cmb.store.reload();

			var r = cmb.store.getAt(0);
			cmb.setValue(r.get('id'), true);
			cmb.fireEvent('select', cmb, r, 0);
		},

		fireComboSelect : function() {
			var cmb = Ext.getCmp('modules-combo');
			cmb.fireEvent('select', cmb, cmb.store.getAt(cmb.selectedIndex),
					cmb.selectedIndex);
		},

		addModule : function() {
			/*
			 * Adicionar un nuevo modulo.
			 */
			xajax_AppHome.exec({
				action : 'AppModules.saveModule',
				enableajax : true,
				args : [[{
					key : 'title',
					value : '-'
				}, {
					key : 'iconcls',
					value : 'icon-16-emblem-generic'
				}, {
					key : 'position',
					value : 1
				}, {
					key : 'collapsed',
					value : 1
				}, {
					key : 'locked',
					value : 0
				}]]
			});
		},

		editModule : function() {
			/*
			 * Editar propiedades del Modulo activo.
			 */
			var cmb = Ext.getCmp('modules-combo');

			if (cmb.getValue() > 0) {

				var frmEdit = new Ext.FormPanel({
					formId : 'frmModEdit',
					labelWidth : 80,
					frame : true,
					modal : false,
					monitorValid : true,
					labelAlign : 'right',
					bodyStyle : 'padding:5px 5px 0',
					defaultType : 'textfield',
					items : [{
						xtype : 'hidden',
						id : 'id',
						name : 'id'
					}, {
						fieldLabel : 'Titulo',
						width : 150,
						id : 'title',
						name : 'title',
						allowBlank : false
					}, {
						fieldLabel : 'IconCls',
						width : 150,
						id : 'iconcls',
						name : 'iconcls',
						allowBlank : true
					}, {
						xtype : 'numberfield',
						fieldLabel : 'Posicion',
						width : 70,
						id : 'position',
						name : 'position',
						allowBlank : false,
						allowDecimals : false,
						allowNegative : false
					}]
				});

				var win = new Ext.Window({
					layout : 'fit',
					width : 300,
					height : 170,
					resizable : false,
					modal : true,
					title : 'Propiedades del Modulo',
					closeAction : 'close',
					plain : true,
					items : frmEdit,
					buttons : [{
						text : 'Guardar',
						formBind : true,
						handler : function() {
							/*
							 * Guardar los valores de formulario.
							 */
							xajax_AppHome.exec({
								action : 'AppModules.updateModule',
								enableajax : true,
								args : [xajax.getFormValues('frmModEdit')]
							});
							win.close();
						}
					}, {
						text : 'Cerrar',
						handler : function() {
							win.close();
						}
					}]
				});

				frmEdit.on('render', function(formpanel) {
					/*
					 * Llamada sincrona para obtener los valores actuales del
					 * modulo.
					 */
					var xValues = xajax.request({
						xjxcls : 'AppHome',
						xjxmthd : 'exec'
					}, {
						mode : 'synchronous',
						parameters : [{
							action : 'AppModules.toJson',
							returnvalue : true,
							args : [cmb.getValue()]
						}]
					});

					formpanel.getForm()
							.setValues(Ext.util.JSON.decode(xValues));

				});

				win.show();
			}
		},

		deleteModule : function() {
			/*
			 * Eliminar el Modulo.
			 */
			var cmb = Ext.getCmp('modules-combo');
			var record = cmb.store.getAt(cmb.selectedIndex);

			Ext.MessageBox.confirm('Eliminar', 'Seguro desea eliminar "'
					+ record.get('title') + '" ?', function(btn) {
				if (btn == 'yes') {
					xajax_AppHome.exec({
						action : 'AppModules.deleteModule',
						enableajax : true,
						args : [record.get('id')]
					});
				}
			});
		},

		addItem : function() {
			var cmb = Ext.getCmp('modules-combo');
			if (cmb.getValue() > 0) {
				/*
				 * Insertar un nuevo item para el modulo seleccionado.
				 */
				xajax_AppHome.exec({
					action : 'AppModuleMenus.saveItem',
					enableajax : true,
					args : [[{
						key : 'id_module',
						value : cmb.getValue()
					}, {
						key : 'text',
						value : ''
					}, {
						key : 'action',
						value : ''
					}, {
						key : 'iconcls',
						value : ''
					}, {
						key : 'position',
						value : 1
					}, {
						key : 'leaf',
						value : 1
					}, {
						key : 'locked',
						value : 0
					}]]
				});
			}
		},

		deleteItem : function() {
			/*
			 * Eliminar Item del Modulo.
			 */
			var record = _grid.getSelectionModel().getSelected();

			Ext.MessageBox.confirm('Eliminar', 'Seguro desea eliminar "'
					+ record.get('text') + '" ?', function(btn) {
				if (btn == 'yes') {
					xajax_AppHome.exec({
						action : 'AppModuleMenus.deleteItem',
						enableajax : true,
						args : [record.get('id')]
					});
				}
			});
		}

	}; // return end

}();