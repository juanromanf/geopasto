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
				queryFunction : MapaUsoSuelosUI.getInfo,
				queryList : MapaUsoSuelosUI.addQueryPanel
			});

			p.on('panelReady', MapaUsoSuelosUI.addCustomButtons);

			_getContainer().add(p);
			_getContainer().doLayout();

		}, // init end
/*
		addCustomButtons : function(oPanel) {
			var tb = oPanel.getTopToolbar();

			tb.addButton({
				text : 'Edicion OFF',
				pressed : false,
				enableToggle : true,
				listeners : {
					toggle : function(btn, pressed) {
						if (pressed) {
							this.setText('Edicion ON');
							oPanel.queryFunction = MapaUsoSuelosUI.editInfoXY;

						} else {
							this.setText('Edicion OFF');
							oPanel.queryFunction = MapaUsoSuelosUI.getInfo;
						}
					}
				}
			});
		},

		editInfoXY : function(x, y) {

			var win = Ext.getCmp('edit-win');

			if (!win) {
				var record = new Ext.data.Record.create([{
					name : 'codareaactividad'
				}, {
					name : 'areaactividad'
				}]);

				var ds = new Ext.data.Store({
					autoLoad : true,
					reader : new Ext.data.JsonReader({
						id : 'codareaactividad'
					}, record),
					proxy : new Ext.data.XajaxProxy({
						xjxcls : 'AppHome',
						xjxmthd : 'exec'
					}),
					baseParams : {
						action : 'SII_PotAreasActividad.getAll',
						returnvalue : true,
						args : [true]
					}
				});

				oPanel = Ext.getCmp('usosuelos-panel');
				x = oPanel.pixelToGeo(x, false);
				y = oPanel.pixelToGeo(y, true);

				var cmb = new Ext.form.ComboBox({
					id : 'areas-combo',
					hiddenName : 'codareaactividad',
					fieldLabel : 'Area Actividad',
					store : ds,
					valueField : 'codareaactividad',
					displayField : 'areaactividad',
					width : 220,
					editable : false,
					allowBlank : false,
					selectOnFocus : true,
					mode : Ext.isIE ? 'local' : 'remote',
					triggerAction : 'all',
					loadingText : 'Cargando...',
					emptyText : 'Seleccione...'
				});

				var frm = new Ext.FormPanel({
					id : 'edit-frm',
					bodyStyle : 'padding: 7px 0 0',
					frame : true,
					border : false,
					monitorValid : true,
					labelWidth : 100,
					labelAlign : 'right',
					defaultType : 'textfield',
					items : [cmb, {
						id : 'txt-predio',
						readOnly : true,
						allowBlank : false,
						fieldLabel : 'Predio',
						width : 220
					}],
					buttons : [{
						formBind : true,
						text : 'Guardar',
						handler : function() {
							var predio = Ext.getCmp('txt-predio').getValue();
							var codarea = cmb.getValue();
							
							xajax.$('usosuelos-x').value = xajax.$('usosuelos-img').width / 2;
							xajax.$('usosuelos-y').value = xajax.$('usosuelos-img').height / 2;

							xajax_AppHome.exec({
								action : 'InfoPredios.modify',
								enableajax : true,
								args : [predio, codarea]
							});
						}
					}, {
						text : 'Cancelar',
						handler : function() {
							win.close();
						}
					}]
				});

				var record1 = new Ext.data.Record.create([{
					name : 'gid'
				}, {
					name : 'numpredio'
				}, {
					name : 'codareaactividad'
				}, {
					name : 'areaactividad'
				}]);

				var ds1 = new Ext.data.Store({
					reader : new Ext.data.JsonReader({}, record1),
					proxy : new Ext.data.XajaxProxy({
						xjxcls : 'AppHome',
						xjxmthd : 'exec'
					}),
					baseParams : {
						action : 'InfoPredios.getInfoXY',
						returnvalue : true,
						args : [x, y]
					}
				});

				win = new Ext.Window({
					id : 'edit-win',
					iconCls : 'icon-16-help-contents',
					layout : 'fit',
					width : 400,
					height : 150,
					resizable : false,
					autoScroll : true,
					modal : false,
					title : 'Edicion',
					closeAction : 'close',
					items : [frm]
				});

				win.on('show', function(oWin) {
					ds1.load({
						callback : function(r, options, sucess) {

							Ext.getCmp('txt-predio').setValue(r[0]
									.get('numpredio'));

							// Ext.getCmp('areas-combo').selectByValue(r[0]
							// .get('codareaactividad'));
						}
					});
				});

				_getContainer().add(win);
				_getContainer().doLayout();
				win.center();
			}

			win.show();
		},
*/
		addQueryPanel : function() {

			var frm = new Ext.FormPanel({
				id : 'usosuelos-qf',
				formId : 'frm-query',
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
					inputValue : 'q-actividad'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Cual es el total del Area Comercial?',
					inputValue : 'area-comercial'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Cual es el total del Area Residencial?',
					inputValue : 'area-residencial'
				}, {
					name : 'active-q',
					boxLabel : '&iquest;Cual es el total del Area Zonas Verdes?',
					inputValue : 'area-zonas-verdes'
				}]
			});

			var qp = new Ext.Panel({
				id : 'usosuelos-query',
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

			var values = Ext.getCmp('usosuelos-qf').getForm().getValues();
			var qId = values['active-q'];
			var win = Ext.getCmp('info-win');

			if (!win) {
				var record = new Ext.data.Record.create([, {
					name : 'seccion'
				}, {
					name : 'property'
				}, {
					name : 'value'
				}, {
					name : 'extra'
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
							query : qId,
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
					id : 'info-grid',
					store : ds,
					colModel : cm,
					loadMask : true,
					enableHdMenu : false,
					view : new Ext.grid.GroupingView({
						forceFit : true,
						groupTextTpl : '{group}'
					})
				});

				// display extra details
				grid.on('cellclick', function(g, rowIndex, columnIndex, e) {

					var r = g.getStore().getAt(rowIndex);
					var data = r.get('extra');
					if (data) {

						// define a template to use for the detail view
						var tplMarkup = [
								'<div class="x-panel-header" style="margin: 5px">{value}</div>',
								'<p style="text-align: justify; margin: 8px; color: #000000;">{extra}<p/>'];
						var siglaTpl = new Ext.Template(tplMarkup);

						var oWin = new Ext.Window({
							id : 'detail-win',
							iconCls : 'icon-16-help-contents',
							layout : 'fit',
							width : 400,
							height : 250,
							collapsible : false,
							resizable : true,
							autoScroll : true,
							modal : true,
							title : 'Detalles',
							closeAction : 'close',
							plain : true,
							items : [{
								id : 'extra-panel',
								xtype : 'panel'
							}]
						});

						oWin.show(g);

						var detailPanel = Ext.getCmp('extra-panel');
						siglaTpl.overwrite(detailPanel.body, r.data);
					}

				});

				win = new Ext.Window({
					id : 'info-win',
					iconCls : 'icon-16-help-contents',
					layout : 'fit',
					width : 400,
					height : 300,
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
				var s = Ext.getCmp('info-grid').getStore();
				s.baseParams.args = [{
					x : x,
					y : y,
					query : qId,
					extent : xajax.$('usosuelos-ex').value
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