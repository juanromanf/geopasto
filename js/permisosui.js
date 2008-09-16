/*
 * PermisosUI Javascript File.
 */

/*
 * Check nodes bug fix.
 */
Ext.override(Ext.tree.TreeNodeUI, {
	toggleCheck : function(value) {
		var cb = this.checkbox;
		if (cb) {
			var checkvalue = (value === undefined ? !cb.checked : value);
			cb.checked = checkvalue;
			this.node.attributes.checked = checkvalue;
		}
	}
});

var PermisosUI = function() {
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

	function _getColumnModel() {
		_colmodel = new Ext.grid.ColumnModel([new Ext.grid.RowNumberer(), {
			header : "Apellidos",
			width : 100,
			sortable : true,
			dataIndex : 'apellidos',
			editor : false
		}, {
			header : "Nombres",
			width : 100,
			sortable : true,
			renderer : Ext.util.Format.capitalize,
			dataIndex : 'nombres',
			editor : false
		}, {
			header : "Usuario",
			width : 100,
			sortable : true,
			dataIndex : 'usuario',
			editor : false
		}]);

		return _colmodel;
	}

	/**
	 * Marca los permisos del usuario en el arbol.
	 */
	function checknodes(numide) {
		var json_usr = xajax.request({
			xjxcls : 'AppHome',
			xjxmthd : 'exec'
		}, {
			mode : 'synchronous',
			parameters : [{
				action : 'Permisos.getRigths',
				returnvalue : true,
				args : [numide, true]
			}]
		});

		var array_check = eval(json_usr);
		var xtree = Ext.getCmp('tree_rigth');

		for (var index = 0; index < array_check.length; index++) {
			var node = xtree.getNodeById(array_check[index].id_menu);
			node.getUI().toggleCheck(true);
		}
	}

	return {
		/*
		 * public properties, e.g. strings to translate public methods.
		 */

		init : function() {

			var xstore = new Ext.data.Store({
				autoLoad : true,
				reader : new Ext.data.JsonReader({}, UsuariosUI.getRecord()),
				proxy : new Ext.data.XajaxProxy({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}),
				baseParams : {
					action : 'Usuarios.getAllUsers',
					returnvalue : true,
					args : [true]
				}
			});

			_grid = new Ext.grid.GridPanel({
				id : 'privileges-grid',
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
					text : 'Asignar privilegios',
					tooltip : 'Editar privilegios del usuario',
					iconCls : 'icon-16-gtk-edit',
					handler : function() {
						PermisosUI.editPrivileges();
					}
				}, '-', {
					text : 'Cerrar',
					handler : function() {
						PermisosUI.closeTab();
					},
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

		closewindow : function() {
			Ext.getCmp('permisos-win').close();
		},

		editPrivileges : function() {
			var g = Ext.getCmp('privileges-grid');
			var record = g.getSelectionModel().getSelected();
			if (record) {
				// ------- Cargar tree -------
				var xtree = new Ext.tree.TreePanel({
					id : 'tree_rigth',
					loader : new Ext.tree.TreeLoader(),
					useArrows : true,
					autoScroll : true,
					animate : true,
					rootVisible : false
				});
				var tree_json = xajax.request({
					xjxcls : 'AppHome',
					xjxmthd : 'exec'
				}, {
					mode : 'synchronous',
					parameters : [{
						action : 'Permisos.getSecurityTree',
						returnvalue : true,
						args : [true]
					}]
				});
				// set the root node
				var xroot = eval('new Ext.tree.AsyncTreeNode(' + tree_json
						+ ')');

				xtree.setRootNode(xroot);

				// -----------------------------------

				var win = new Ext.Window({
					id : 'permisos-win',
					width : 300,
					height : 300,
					resizable : true,
					modal : true,
					layout : 'fit',
					autoScroll : true,
					title : 'Privilegios de ' + record.get('nombres'),
					closeAction : 'close',
					plain : true,
					items : xtree,
					buttons : [{
						text : 'Guardar',
						formBind : true,
						handler : function() {
							/**
							 * funcion para recorrer el arbol y asignar los
							 * permisos.
							 */

							var check = xtree.getChecked();
							var menuItems = new Array();
							for (var index = 0; index < check.length; index++) {
								menuItems[index] = check[index].id;
							}
							var numide = record.get('numide');
							xajax_AppHome.exec({
								action : 'Permisos.setRigth',
								args : [numide, menuItems],
								enableajax : true
							});
						}
					}, {
						text : 'Cancelar',
						handler : function() {
							win.close();
						}
					}]
				});
				win.show();
				checknodes(record.get('numide'));
			}// fin if
		}

	}; // return end
}();