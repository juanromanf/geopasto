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
				classUI : 'MapaAmenazasUI',
				queryFunction : MapaAmenazasUI.getInfo,
				queryList : MapaAmenazasUI.addQueryPanel
			});

			_getContainer().add(p);
			_getContainer().doLayout();

		}, // init end

		addQueryPanel : function() {

			var frm = new Ext.FormPanel({
				id : 'amenazas-qf',
				bodyStyle : 'padding: 7px 0 0',
				frame : true,
				border : false,
				defaultType : 'radio',
				labelWidth : 1,
				labelSeparator : '&nbsp;',
				items : [{
					name : 'active-q',
					boxLabel : '&iquest;Que Zona corresponde?',
					checked : true,
					inputValue : 'q-zona'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Que Comuna corresponde?',
					inputValue : 'q-comuna'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Que Area Morfologica corresponde?',
					inputValue : 'q-area-homo'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Que datos del Predio existen?',
					inputValue : 'q-amenazas'
				}]
			});

			var qp = new Ext.Panel({
				id : 'amenazas-query',
				iconCls : 'icon-16-help-contents',
				title : 'Informaci&oacute;n Util',
				autoScroll : true,
				layout : 'fit',
				border : false,
				collapsed : true,
				tbar : [{
					text : 'Ayuda',
					iconCls : 'icon-16-help-browser',
					handler : function() {

						Ext.Msg.show({
							icon : Ext.MessageBox.INFO,
							buttons : Ext.Msg.OK,
							title : 'Informaci&oacute;n Util: Ayuda',
							msg : '1. Seleccione una de las consultas disponibles.<br>'
									+ '2. Verifique que la herramienta consulta est&aacute; activa.<br>'
									+ '3. Click en un punto del mapa para obtener una respuesta.'
						});
					}
				}],
				items : [frm]
			});

			return qp;
		},

		getInfo : function(x, y) {

			var values = Ext.getCmp('amenazas-qf').getForm().getValues();
			var qId = values['active-q'];
			var win = Ext.getCmp('amenazas-info-win');

			if (!win) {
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
						action : 'MapaAmenazasUI.doQuery',
						returnvalue : true,
						enableajax : true,
						args : [{
							x : x,
							y : y,
							query : qId,
							extent : xajax.$('amenazas-ex').value
						}]
					}
				});

				var cm = new Ext.grid.ColumnModel([{
					header : "&nbsp;",
					dataIndex : 'property',
					css : 'text-align: right; font-weight: bold;',
					width : 120,
					sortable : false,
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
					id : 'amenazas-info-grid',
					store : ds,
					colModel : cm,
					loadMask : true,
					enableHdMenu : false,
					view : new Ext.grid.GroupingView({
						forceFit : true,
						groupTextTpl : '{group}'
					})
				});

				win = new Ext.Window({
					id : 'amenazas-info-win',
					iconCls : 'icon-16-help-contents',
					layout : 'fit',
					width : 400,
					height : 250,
					collapsible : true,
					resizable : true,
					autoScroll : true,
					modal : false,
					title : 'Informaci&oacute;n Util',
					closeAction : 'close',
					plain : true,
					items : grid
				});
				_getContainer().add(win);
				_getContainer().doLayout();
				win.center();

			} else {
				var s = Ext.getCmp('amenazas-info-grid').getStore();
				s.baseParams.args = [{
					x : x,
					y : y,
					query : qId,
					extent : xajax.$('amenazas-ex').value
				}];

				s.reload();
			}
			win.show();
		},

		closeTab : function() {
			_close();
		}

	}; // return end

}();