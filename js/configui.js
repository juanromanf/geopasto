/*
 * ConfigUI Javascript File
 */

var ConfigUI = function() {
	/*
	 * do NOT access DOM from here; elements don't exist yet.
	 * 
	 * private variables. private functions
	 */

	var myForm = null;	
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

			/*
			 * Store para Themes disponibles.
			 */
			var storeThemes = new Ext.data.SimpleStore({
				fields : ['theme_key', 'theme_text'],
				data : [['', 'Default'], ['xtheme-black.css', 'Black'],
						['xtheme-darkgray.css', 'Dark Gray'],
						['xtheme-gray.css', 'Gray'],
						['xtheme-olive.css', 'Olive'],
						['xtheme-purple.css', 'Purple'],
						['xtheme-slate.css', 'Slate'],
						['xtheme-slickness.css', 'Slickness'],]
			});

			// Store para Servidores de mapas disponibles.
			var store = new Ext.data.SimpleStore({
				fields : ['server_key', 'server_text'],
				data : [['atlas', 'AtlasServer'], ['mapserver', 'MapServer 5'],]
			});

			myForm = new Ext.FormPanel({
				formId : 'frmConfig',
				labelWidth : 80,
				monitorValid : true,
				labelAlign : 'right',
				title : 'Configuraciones Generales',
				bodyStyle : 'padding:5px 5px 0',
				width : 350,
				items : [{
					xtype : 'fieldset',
					title : 'Apariencia de Aplicacion',
					autoHeight : true,
					collapsible : false,
					defaults : {
						width : 210
					},
					items : [{
						xtype : 'combo',
						id : 'theme_combo',
						hiddenName : 'theme_name',
						fieldLabel : 'Theme',
						store : storeThemes,
						displayField : 'theme_text',
						valueField : 'theme_key',
						typeAhead : true,
						editable : false,
						allowBlank : false,
						mode : 'local',
						triggerAction : 'all',
						emptyText : 'Seleccione un estilo...',
						selectOnFocus : true,
						listeners : {
							select : function(combo, record, index) {
								var theme = record.get('theme_key');
								Ext.util.CSS.swapStyleSheet('theme-css',
										'include/ext/resources/css/' + theme);
							}
						}
					}]
				}, {
					xtype : 'fieldset',
					title : 'Servidor de Mapas',
					autoHeight : true,
					collapsible : false,
					defaults : {
						width : 210
					},
					defaultType : 'textfield',
					items : [{
						xtype : 'combo',
						id : 'server_combo',
						hiddenName : 'server_name',
						fieldLabel : 'Utilizar',
						store : store,
						displayField : 'server_text',
						valueField : 'server_key',
						typeAhead : true,
						editable : false,
						allowBlank : false,
						mode : 'local',
						triggerAction : 'all',
						emptyText : 'Seleccione un servidor...',
						selectOnFocus : true
					}]
				}, {
					xtype : 'fieldset',
					title : 'Duraci&oacute;n de la Sesion',
					collapsible : false,
					autoHeight : true,
					defaults : {
						width : 80
					},
					defaultType : 'textfield',
					items : [{
						xtype : 'numberfield',
						fieldLabel : 'Minutos',
						id : 'sesion_time',
						name : 'sesion_time',
						value : '30',
						allowBlank : false,
						allowDecimals : false,
						allowNegative : false
					}]
				}],
				buttons : [{
					text : 'Guardar',
					iconCls : 'icon-16-dialog-ok',
					formBind : true,
					handler : function() {
						/*
						 * Guardar los valores del formulacion en la
						 * configuracion.
						 */
						xajax_AppHome.exec({
							action : 'Config.saveConfig',
							enableajax : true,
							args : [xajax.getFormValues(myForm.formId)]
						});
					}
				}, {
					text : 'Cerrar',
					iconCls : 'icon-16-dialog-close',
					handler : function() {
						_close();
					}
				}]
			});

			/*
			 * Llamada sincrona para obtener los valores actuales de
			 * configuracion.
			 */
			var xValues = xajax.request({
				xjxcls : 'AppHome',
				xjxmthd : 'exec'
			}, {
				mode : 'synchronous',
				parameters : [{
					action : 'Config.getConfigArray',
					returnvalue : true
				}]
			});

			_getContainer().add(myForm);
			_getContainer().doLayout();

			// Cargar los valores actuales de configuracion en el formulario.
			myForm.getForm().setValues(eval(xValues));
		}, // init end

		closeTab : function() {
			_close();
		}

	}; // end return
}();