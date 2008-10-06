/**
 * Especializacion de la clase Panel.
 */

Ext.MapPanel = Ext.extend(Ext.Panel, {
	ddImage : false,
	imageBegin : null,
	dragBegin : null,
	mouseBegin : null,
	queryFunction : Ext.emptyFn,
	queryList : new Ext.Panel({
		title : 'Consultas',
		border : false,
		collapsed : true
	}),

	initComponent : function() {
		/*
		 * Store para tamanios disponibles.
		 */
		var ds = new Ext.data.SimpleStore({
			fields : ['text', 'value'],
			data : [['640 x 480', '640x480'], ['800 x 600', '800x600'],
					['1024 x 768', '1024x768']]
		});

		var cmb = new Ext.form.ComboBox({
			id : 'size-combo',
			hiddenName : 'map-size',
			listClass : 'x-combo-list-small',
			store : ds,
			width : 100,
			displayField : 'text',
			valueField : 'value',
			typeAhead : true,
			editable : false,
			allowBlank : true,
			mode : 'local',
			triggerAction : 'all',
			emptyText : 'ancho x alto',
			selectOnFocus : true
		});

		cmb.on('select', function(combo, record, index) {
			this.maskPanel(true);

			xajax_AppHome.exec({
				action : this.classUI + '.resizeMap',
				enableajax : true,
				args : [record.get('value')]
			});
		}, this);

		var tb = new Ext.Toolbar({
			items : ['&nbsp;Tama&ntilde;o del mapa: ', cmb, '-',
					'<span id="' + this.mapname + '-scale">&nbsp;</span>', '-',
					{
						text : '',
						tooltip : 'Herramienta restaurar',
						iconCls : 'icon-16-zoom-original',
						handler : function() {

							this.setActiveAction('pan');
							xajax.$(this.mapname + '-ex').value = xajax
									.$(this.mapname + '-oe').value;
							xajax.$(this.mapname + '-x').value = xajax
									.$(this.mapname + '-img').width
									/ 2;
							xajax.$(this.mapname + '-y').value = xajax
									.$(this.mapname + '-img').height
									/ 2;
							this.onMouseClick();
						},
						scope : this
					}, {
						id : this.mapname + '-btn-pan',
						text : '',
						tooltip : 'Herramienta navegar',
						iconCls : 'icon-16-zoom-best-fit',
						pressed : true,
						enableToggle : true,
						toggleGroup : 'map-tools',
						handler : function() {
							this.setActiveAction('pan');
						},
						scope : this
					}, {
						id : this.mapname + '-btn-zi',
						text : '',
						tooltip : 'Herramienta acercar',
						iconCls : 'icon-16-zoom-in',
						enableToggle : true,
						toggleGroup : 'map-tools',
						handler : function() {
							this.setActiveAction('zoom-in');
						},
						scope : this
					}, {
						id : this.mapname + '-btn-zo',
						text : '',
						tooltip : 'Herramienta alejar',
						iconCls : 'icon-16-zoom-out',
						enableToggle : true,
						toggleGroup : 'map-tools',
						handler : function() {
							this.setActiveAction('zoom-out');
						},
						scope : this
					}, '-', {
						id : this.mapname + '-btn-info',
						text : '',
						tooltip : 'Herramienta consulta',
						enableToggle : true,
						toggleGroup : 'map-tools',
						iconCls : 'icon-16-help-contents',
						handler : function() {
							this.setActiveAction('query');
							Ext.getCmp(this.mapname + '-query').expand(true);
						},
						scope : this
					}, '-', {
						text : '',
						tooltip : 'Guardar mapa',
						iconCls : 'icon-16-media-floppy',
						handler : function() {
							this.saveImage();
						},
						scope : this
					}]
		});

		var tools = new Ext.Panel({
			id : this.mapname + '-east',
			region : 'east',
			title : "Herramientas",
			iconCls : 'icon-16-emblem-generic',
			border : true,
			collapsed : false,
			collapsible : true,
			width : 250,
			margins : '0 5px 0 0',
			split : true,
			layout : 'accordion',
			layoutConfig : {
				animate : true
			}
		});

		var navigation = new Ext.Panel({
			region : 'center',
			border : false,
			tbar : tb,
			autoScroll : true,
			iconCls : 'icon-16-home',
			html : '<div id="' + this.mapname + '-div">&nbsp;</div>'
		});

		this.layout = 'border';
		this.frameElement = true;
		this.border = false;
		this.items = [navigation, tools];
		this.height = this.getContainer().getEl().getHeight() - 2;

		Ext.MapPanel.superclass.initComponent.call(this);
	},

	getSearchForm : function(panel) {

		var r = new Ext.data.Record.create([{
			name : 'id'
		}, {
			name : 'text'
		}, {
			name : 'icon'
		}, {
			name : 'leaf'
		}, {
			name : 'checked'
		}]);

		var ds = new Ext.data.Store({
			autoLoad : true,
			reader : new Ext.data.JsonReader({}, r),
			proxy : new Ext.data.XajaxProxy({
				xjxcls : 'AppHome',
				xjxmthd : 'exec'
			}),
			baseParams : {
				action : this.classUI + '.getLayers',
				returnvalue : true
			}
		});

		var cmb = new Ext.form.ComboBox({
			id : panel.mapname + '-layers-combo',
			hiddenName : panel.mapname + '-active-layer',
			listClass : 'x-combo-list-small',
			store : ds,
			width : 120,
			listWidth : '120',
			fieldLabel : 'Capa',
			displayField : 'text',
			valueField : 'text',
			typeAhead : true,
			editable : false,
			allowBlank : false,
			mode : Ext.isIE ? 'local' : 'remote',
			triggerAction : 'all',
			emptyText : '...',
			selectOnFocus : true
		});

		var frm = new Ext.FormPanel({
			id : panel.mapname + '-search-frm',
			bodyStyle : 'padding: 7px 0 0',
			frame : true,
			border : false,
			labelWidth : 50,
			labelAlign : 'right',
			defaultType : 'textfield',
			items : [cmb, {
				fieldLabel : 'Texto',
				width : 120,
				id : panel.mapname + 'search-text',
				allowBlank : false
			}, {
				xtype : 'button',
				id : panel.mapname + '-search-btn',
				fieldLabel : '',
				labelSeparator : '',
				text : 'Buscar',
				iconCls : 'icon-16-edit-find',
				isFormField : true,
				handler : function() {

					var layer = cmb.getValue();
					var search = Ext.getCmp(panel.mapname + 'search-text')
							.getValue();

					if (layer == '') {
						Ext.MessageBox.alert('Busqueda rapida',
								'Seleccione una capa para la busqueda.');
					} else if (search == '') {
						Ext.MessageBox.alert('Busqueda rapida',
								'Escriba un texto para la busqueda.');
					} else {
						this.showSearchResults(layer, search);
					}
				},
				scope : panel
			}]
		});

		var p = new Ext.Panel({
			iconCls : 'icon-16-edit-find',
			title : "Busqueda R&aacute;pida",
			layout : 'fit',
			autoScroll : true,
			border : false,
			collapsed : true,
			items : [frm]
		});

		return p;
	},

	showSearchResults : function(layer, search) {

		var ds = new Ext.data.Store({
			autoLoad : true,
			reader : new Ext.data.JsonReader({
				root : 'rows',
				totalProperty : 'total'
			}),
			proxy : new Ext.data.XajaxProxy({
				xjxcls : 'AppHome',
				xjxmthd : 'exec'
			}),
			baseParams : {
				action : this.classUI + '.quickSearch',
				returnvalue : true,
				enableajax : true,
				args : [{
					layer : layer,
					text : search
				}]
			}
		});

		// Create the grid
		var grid = new Ext.grid.AutoGridPanel({
			store : ds,
			loadMask : true,
			selModel : new Ext.grid.RowSelectionModel(),
			trackMouseOver : true,
			viewConfig : {
				emptyText : 'No hay resultados...'
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

		/**
		 * Ubicar Resultado en el Mapa.
		 */
		grid.addListener("celldblclick", function(g, rIndex, cIndex, e) {

			var r = grid.getSelectionModel().getSelected();

			xajax.$(this.mapname + '-action').value = 'pan';
			xajax.$(this.mapname + '-ex').value = r.get('extent');
			xajax.$(this.mapname + '-x').value = xajax.$(this.mapname + '-img').width
					/ 2;
			xajax.$(this.mapname + '-y').value = xajax.$(this.mapname + '-img').height
					/ 2;
			this.onMouseClick();

		}, this);

		var win = new Ext.Window({
			layout : 'fit',
			width : 300,
			height : 170,
			resizable : true,
			autoScroll : true,
			modal : false,
			title : layer + ' : Resultados para "' + search + '"',
			closeAction : 'close',
			plain : true,
			items : grid
		});

		win.show();

		ds.on("load", function(store, records, options) {
			grid.reconfigure(store, grid.colModel);
		});
	},

	onRender : function(ct, position) {
		Ext.MapPanel.superclass.onRender.call(this, ct, position);
		this.maskPanel(true);

		var js = "Ext.getCmp('" + this.mapname + "-panel').addListeners();";

		xajax_AppHome.exec({
			action : this.classUI + '.createLayout',
			target : this.mapname + '-div',
			enableajax : true,
			args : [{
				map : this.mapfile
			}],
			jscallback : js
		});
	},

	getImage : function() {
		var img = Ext.get(this.mapname + '-img');
		return img;
	},

	getView : function() {
		var view = Ext.get(this.mapname + '-div');
		return view;
	},

	saveImage : function() {
		var img = this.getImage();
		var url = "download.php?id=" + img.dom.src;
		window.open(url, "_blank", 'width=150,height=70');
	},

	addImagePopup : function() {
		var contextMenu = new Ext.menu.Menu({
			items : [{
				text : 'Restaurar',
				iconCls : 'icon-16-zoom-original',
				handler : function() {

					this.setActiveAction('pan');
					xajax.$(this.mapname + '-ex').value = xajax.$(this.mapname
							+ '-oe').value;
					xajax.$(this.mapname + '-x').value = xajax.$(this.mapname
							+ '-img').width
							/ 2;
					xajax.$(this.mapname + '-y').value = xajax.$(this.mapname
							+ '-img').height
							/ 2;
					this.onMouseClick();
				},
				scope : this
			}, {
				text : 'Navegar',
				iconCls : 'icon-16-zoom-best-fit',
				handler : function() {
					this.setActiveAction('pan');
				},
				scope : this
			}, {
				text : 'Acercar',
				iconCls : 'icon-16-zoom-in',
				handler : function() {
					this.setActiveAction('zoom-in');
				},
				scope : this
			}, {
				text : 'Alejar',
				tooltip : 'Herramienta alejar',
				iconCls : 'icon-16-zoom-out',
				handler : function() {
					this.setActiveAction('zoom-out');
				},
				scope : this
			}, '-', {
				text : 'Consulta',
				iconCls : 'icon-16-help-contents',
				handler : function() {
					this.setActiveAction('query');
					Ext.getCmp(this.mapname + '-query').expand(true);
				},
				scope : this
			}, '-', {
				text : 'Guardar mapa',
				iconCls : 'icon-16-media-floppy',
				handler : function() {
					this.saveImage();
				},
				scope : this
			}, {
				text : 'Imprimir mapa',
				iconCls : 'icon-16-printer',
				handler : function() {
				},
				scope : this
			},]
		});

		var img = this.getImage();

		img.on('contextmenu', function(event) {
			event.stopEvent();
			contextMenu.showAt(event.getXY());
		});
	},

	addListeners : function() {

		var tree = this.getLayersTree();
		var findForm = this.getSearchForm(this);

		var east = Ext.getCmp(this.mapname + '-east');
		east.add(tree);
		east.add(findForm);
		east.add(this.queryList());
		east.doLayout();

		this.addImagePopup();

		var tip = Ext.getCmp(this.mapname + '-ttip');
		if (tip) {
			tip.destroy();
		}
		new Ext.ToolTip({
			id : this.mapname + '-ttip',
			target : this.mapname + '-img',
			header : true,
			width : 100,
			height : 20,
			dismissDelay : 0,
			showDelay : 50,
			trackMouse : true
		});

		var view = this.getView();
		var img = this.getImage();
		this.imageBegin = [img.getLeft(), img.getTop()];

		img.on('load', function() {
			img.setOpacity(1, true);
		});

		var w = (img.getWidth() + 10) + "px";
		var h = (img.getHeight() + 10) + "px";

		view.setStyle('width', w);
		view.setStyle('height', h);
		view.setStyle('position', 'relative');
		view.setStyle('overflow', 'hidden');
		view.setStyle('margin', '5px auto 5px auto');
		view.setStyle('border', '1px solid #CCCCCC');
		img.setStyle('margin', '1px');
		img.setStyle('cursor', 'move');

		view.addListener('click', this.onMouseClick, this);
		view.addListener('mousewheel', this.onMouseWheel, this);
		view.addListener('mousemove', this.onMouseMove, this);

		var dd = new Ext.dd.DD(img);
		dd.onMouseDown = this.onMouseDown.createDelegate(this);
		dd.onDrag = this.onDragImage.createDelegate(this);
		dd.startDrag = this.onStartDrag.createDelegate(this);
		dd.endDrag = this.onEndDrag.createDelegate(this);

	},

	onStartDrag : function(x, y) {

		var img = this.getImage();
		this.imageBegin = [img.getLeft(), img.getTop()];
	},

	onEndDrag : function(e) {
		this.onDragImage(e);
		this.onMouseClick();
	},

	onDragImage : function(e) {

		var img = this.getImage();
		var dx, dy, x, y;

		if (e.getPageX() > this.mouseBegin[0]) {
			dx = e.getPageX() - this.mouseBegin[0];
			x = (img.getWidth() / 2) - dx;
		} else {
			dx = this.mouseBegin[0] - e.getPageX();
			x = (img.getWidth() / 2) + dx;
		}

		if (e.getPageY() > this.mouseBegin[1]) {
			dy = e.getPageY() - this.mouseBegin[1];
			y = (img.getHeight() / 2) - dy;
		} else {
			dy = this.mouseBegin[1] - e.getPageY();
			y = (img.getHeight() / 2) + dy;
		}

		xajax.$(this.mapname + '-x').value = x;
		xajax.$(this.mapname + '-y').value = y;
	},

	onMouseDown : function(e) {

		this.mouseBegin = [e.getPageX(), e.getPageY()];
	},

	onMouseMove : function(e) {
		var img = this.getImage();
		var x = e.getPageX() - img.getLeft();
		var y = e.getPageY() - img.getTop();

		xajax.$(this.mapname + '-x').value = x;
		xajax.$(this.mapname + '-y').value = y;

		var tip = Ext.getCmp(this.mapname + '-ttip');
		var title = "<center>(" + Math.round(x) + ", " + Math.round(y)
				+ ")</center>";
		tip.setTitle(title);
	},

	onMouseWheel : function(e) {
		var delta = e.getWheelDelta();
		if (delta < 0) {
			this.setActiveAction('zoom-in');
		} else {
			this.setActiveAction('zoom-out');
		}
		var img = this.getImage();
		this.imageBegin = [img.getLeft(), img.getTop()];
		this.onMouseClick();
		this.setActiveAction('pan');
		e.stopEvent();
	},

	onMouseClick : function() {

		var action = this.getActiveAction();
		var x, y;
		x = xajax.$(this.mapname + '-x').value;
		y = xajax.$(this.mapname + '-y').value;

		if (action == 'query') {
			this.queryFunction(x, y);
			return;
		}

		var img = this.getImage();
		img.setOpacity(0, true);
		this.maskPanel(true);

		var js = "var img = Ext.get('" + this.mapname + "-img');" + "img.setX("
				+ this.imageBegin[0] + "); img.setY(" + this.imageBegin[1]
				+ ");";

		xajax_AppHome.exec({
			action : this.classUI + '.doAction',
			enableajax : true,
			args : [{
				action : action,
				extent : xajax.$(this.mapname + '-ex').value,
				x : x,
				y : y
			}],
			jscallback : js
		});
	},

	getSelectedNode : function() {
		var tree = Ext.getCmp(this.mapname + "-tree");
		var node = tree.getSelectionModel().getSelectedNode();

		if (!node) {
			var root = tree.getRootNode();
			tree.getSelectionModel().select(root);
			node = tree.getSelectionModel().selectNext();
		}

		return node;
	},

	getLayersTree : function() {

		var r = new Ext.data.Record.create([{
			name : 'id'
		}, {
			name : 'text'
		}, {
			name : 'iconCls'
		}, {
			name : 'checked'
		}, {
			name : 'expanded'
		}, {
			name : 'leaf'
		}, {
			name : 'children'
		}]);

		var ds = new Ext.data.Store({
			reader : new Ext.data.JsonReader({}, r),
			proxy : new Ext.data.XajaxProxy({
				xjxcls : 'AppHome',
				xjxmthd : 'exec'
			}),
			baseParams : {
				action : this.classUI + '.getLayers',
				returnvalue : true
			}
		});

		var myloader = new Ext.tree.TreeStoreLoader({
			store : ds
		});

		// set the root node
		var root = new Ext.tree.AsyncTreeNode({
			text : 'Capas',
			loader : myloader,
			expanded : true
		});

		var tree = new Ext.tree.TreePanel({
			id : this.mapname + "-tree",
			iconCls : 'icon-16-emblem-photos',
			title : "Capas de Informaci&oacute;n",
			collapsed : false,
			useArrows : true,
			autoScroll : true,
			root : root,
			border : false,
			animate : true,
			rootVisible : true,
			width : 200,
			containerScroll : true,
			tbar : [{
				text : '',
				tooltip : 'Expandir todas las capas',
				iconCls : 'icon-16-expand-all',
				handler : function() {
					tree.expandAll();
				}
			}, '-', {
				text : '',
				tooltip : 'Contraer todas las capas',
				iconCls : 'icon-16-collapse-all',
				handler : function() {
					tree.collapseAll();
				}
			}]
		});

		var contextMenu = new Ext.menu.Menu({
			id : 'popupMenu',
			items : [{
				text : 'Ocultar/Mostrar items',
				iconCls : 'icon-16-draw-brush',
				handler : function() {

					var n = tree.getSelectionModel().getSelectedNode();
					tree.suspendEvents();
					if (!n.isLeaf()) {
						this.toggleItems(n, 'toggle-all-classes');
					}
					tree.resumeEvents();

				},
				scope : this
			}]
		});

		tree.on('contextmenu', function(node, event) {
			if (!node.isLeaf()) {
				event.stopEvent();
				tree.getSelectionModel().select(node);
				contextMenu.showAt(event.getXY());
			}
		}, tree);

		tree.on('checkchange', function(node, checked) {

			tree.getSelectionModel().select(node);
			var n = tree.getSelectionModel().getSelectedNode();

			if (node.isLeaf()) {
				this.toggleItems(n, 'toggle-class');
			} else {
				this.toggleItems(n, 'toggle-layer');
			}

		}, this);

		return tree;
	},

	toggleItems : function(node, process) {

		var params;
		var img = Ext.get(this.mapname + '-img');
		img.setOpacity(0, true);
		this.maskPanel(true);

		switch (process) {
			case 'toggle-layer' :
				params = [{
					action : process,
					layer : node.text
				}];
				break;

			case 'toggle-class' :
				params = [{
					action : process,
					layer : node.parentNode.text,
					classi : node.text
				}];
				break;

			case 'toggle-all-classes' :

				node.eachChild(function(n) {
					n.getUI().toggleCheck();
				});

				params = [{
					action : process,
					layer : node.text
				}];
				break;
		}

		xajax_AppHome.exec({
			action : this.classUI + '.doAction',
			enableajax : true,
			args : params
		});
	},

	maskPanel : function(enable) {
		if (enable) {
			this.getEl()
					.mask('Cargando, por favor espere...', 'x-mask-loading');
		} else {
			this.getEl().unmask();
			if (Ext.isIE) {
				this.doLayout();
			}
		}
	},

	getActiveAction : function() {
		return xajax.$(this.mapname + '-action').value;
	},

	setActiveAction : function(action) {
		var id = this.mapname;
		xajax.$(this.mapname + '-action').value = action;

		var dd = Ext.dd.DragDropMgr.getDDById(this.mapname + '-img');
		var img = this.getImage();
		var ttip = Ext.getCmp(this.mapname + '-ttip');

		switch (action) {
			case 'zoom-in' :
				id += '-btn-zi';
				img.setStyle('cursor', 'crosshair');
				dd.unlock();
				break;

			case 'zoom-out' :
				id += '-btn-zo';
				img.setStyle('cursor', 'crosshair');
				dd.unlock();
				break;

			case 'pan' :
				id += '-btn-pan';
				img.setStyle('cursor', 'move');
				dd.unlock();
				break;

			case 'query' :
				id += '-btn-info';
				img.setStyle('cursor', 'help');
				dd.lock();
				break;
		}
		Ext.getCmp(id).toggle(true);
	},

	getContainer : function() {
		var container = Ext.getCmp('center-panel').getActiveTab();
		return container;
	},

	closeTab : function() {
		Ext.getCmp('center-panel').remove(this.getContainer(), true);
	}
});

Ext.reg('mapanel', Ext.MapPanel);