/**
 * UsuariosUI Javascript File
 */

Ext.BLANK_IMAGE_URL = './img/s.gif'; // Url imagen transparente.

var UsuariosUI = function() {
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

	function _getUserRecord() {
		_record = new Ext.data.Record.create([{
			name : 'numide'
		}, {
			name : 'nombres'
		}, {
			name : 'apellidos'
		}, {
			name : 'usuario'
		}, {
			name : 'passwd'
		}]);

		return _record;
	}

	function _getColumnModel() {
		_colmodel = new Ext.grid.ColumnModel([new Ext.grid.RowNumberer(), /*
																			 * {
																			 * header :
																			 * "Id",
																			 * width :
																			 * 40,
																			 * hidden :
																			 * true,
																			 * sortable :
																			 * true,
																			 * dataIndex :
																			 * 'id' },
																			 */
		{
			header : 'Identificacion',
			width : 70,
			dataIndex : 'numide',
			sortable : true,
			editor : false
		}, {
			header : "Apellidos",
			width : 100,
			sortable : true,
			//renderer : Ext.util.Format.capitalize,
			dataIndex : 'apellidos',
			editor : false
		}, {
			header : "Nombres",
			width : 100,
			sortable : true,
			//renderer : Ext.util.Format.capitalize,
			dataIndex : 'nombres',
			editor : false
		}, {
			header : "Usuario",
			width : 100,
			sortable : true,
			dataIndex : 'usuario',
			editor : false
		}, {
			header : "contraseña",
			width : 100,
			sortable : true,
			dataIndex : 'passwd',
			hidden : true,
			editor : false
		}]);

		return _colmodel;
	}

	return {

		init : function() {

			var xstore = new Ext.data.Store({
				autoLoad : true,
				reader : new Ext.data.JsonReader({}, _getUserRecord()),
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

			_grid = new Ext.grid.EditorGridPanel({
				id : 'users-grid',
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
				bbar : [],
				plugins : [new Ext.ux.grid.Search({
					searchText : 'Filtro',
					mode : 'local',
					iconCls : 'icon-16-edit-find',
					dateFormat : 'Y-m-d',
					minLength : 1
				})]
			/*
			 * , listeners : { afteredit : function(obj) { xajax_AppHome.exec({
			 * action : 'Usuarios.updateUser', enableajax : true, args : [[{ key :
			 * 'id', value : obj.record.get('id') }, { key : obj.field, value :
			 * obj.value }]] }); } }
			 */
			});

			var tb = new Ext.Toolbar({
				items : [
				/*
				 * { text : 'Nuevo usuario', tooltip : 'Adicionar un usuario.',
				 * iconCls : 'icon-16-list-add-user', handler : function() {
				 * UsuariosUI.addUser(); } }, { text : 'Eliminar usuario',
				 * tooltip : 'Eliminar un usuario del sistema.', iconCls :
				 * 'icon-16-list-remove-user', handler : function() {
				 * UsuariosUI.deleteUser(); } }, '-',
				 */{
					text : 'Cerrar',
					handler : function() {
						UsuariosUI.closeTab();
					},
					tooltip : 'Cerrar esta pesta&ntilde;a.',
					iconCls : 'icon-16-dialog-close'
				}]
			});

			_getContainer().add(tb);
			_getContainer().add(_grid);
			_getContainer().doLayout();

		},// fin del init
		/*
		 * addUser : function() { xajax_AppHome.exec({ action :
		 * 'Usuarios.addUser', enableajax : true, args : [[{ key : 'name', value : '' }, {
		 * key : 'login', value : '' }, { key : 'passwd', value : '' }, { key :
		 * 'created', value : '' }, { key : 'modified', value : '' }, { key :
		 * 'active', value : 1 }, { key : 'locked', value : 0 }]] }); },
		 * 
		 * deleteUser : function() { var g = Ext.getCmp('users-grid'); var
		 * record = g.getSelectionModel().getSelected();
		 * Ext.MessageBox.confirm('Eliminar', 'Seguro desea eliminar el usuario "' +
		 * record.get('name') + '" ?', function(btn) { if (btn == 'yes') {
		 * xajax_AppHome.exec({ action : 'Usuarios.deleteUser', enableajax :
		 * true, args : [record.get('id')] }); } }); },
		 */

		displayLogin : function() {

			var login = new Ext.FormPanel({
				id : 'frmLogin',
				labelWidth : 80,
				labelAlign : 'right',
				formId : 'frmlogin',
				defaultType : 'textfield',
				monitorValid : true,
				bodyStyle : {
					position : 'relative'
				},
				items : [{
					fieldLabel : 'Usuario',
					id : 'user_txt',
					name : 'users_login',
					allowBlank : false
				}, {
					fieldLabel : 'Contrase&ntilde;a',
					id : 'pass_txt',
					name : 'users_passwd',
					inputType : "password",
					allowBlank : false

				}],
				buttons : [{
					id : 'btnLogin',
					text : 'Iniciar Sesion',
					iconCls : 'icon-16-document-decrypt',
					formBind : true,
					handler : function() {
						xajax_AppHome.exec({
							action : 'Usuarios.doLogin',
							enableajax : true,
							args : [xajax.getFormValues('frmlogin')]
						});
					}
				}]
			});

			var p = new Ext.Panel({
				title : 'Iniciar Sesion',
				renderTo : 'container-login',
				iconCls : 'icon-16-document-encrypt',
				frame : true,
				width : 300,
				items : login
			});

			Ext.get('container-login').center(Ext.getBody());
			Ext.EventManager.onWindowResize(function() {
				Ext.getCmp('pass_txt').focus();
				Ext.getCmp('user_txt').focus();
			}, p);

			var map = new Ext.KeyMap("frmLogin", {
				key : 13, // or Ext.EventObject.ENTER
				fn : Ext.getCmp('btnLogin').handler
			});

		},// fin display login

		closeTab : function() {
			_close();
		},

		reloadUsers : function() {
			var s = Ext.getCmp('users-grid').getStore();
			s.reload();
		},

		getRecord : function() {
			return _getUserRecord();
		}

	}; // return end

}();