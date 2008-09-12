/**
 * Especializacion de la clase Panel.
 */

Ext.MapPanel = Ext.extend(Ext.Panel, {
	ddImage : false,
	imageBegin : null,
	dragBegin : null,
	mouseBegin : null,

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
			items : ['&nbsp;Tama&ntilde;o: ', cmb, '-', {
				id : this.mapname + '-btn-pan',
				text : 'Desplazar',
				iconCls : 'icon-16-zoom-best-fit',
				pressed : true,
				enableToggle : true,
				toggleGroup : 'map-tools',
				handler : function() {
					xajax.$(this.mapname + '-action').value = 'pan';
				},
				scope : this
			}, '-', {
				id : this.mapname + '-btn-zi',
				text : 'Acercar',
				iconCls : 'icon-16-zoom-in',
				enableToggle : true,
				toggleGroup : 'map-tools',
				handler : function() {
					xajax.$(this.mapname + '-action').value = 'zoom-in';
				},
				scope : this
			}, '-', {
				id : this.mapname + '-btn-zo',
				text : 'Alejar',
				iconCls : 'icon-16-zoom-out',
				enableToggle : true,
				toggleGroup : 'map-tools',
				handler : function() {
					xajax.$(this.mapname + '-action').value = 'zoom-out';
				},
				scope : this
			}, '-', {
				text : 'Restaurar',
				iconCls : 'icon-16-zoom-original',
				handler : function() {

					xajax.$(this.mapname + '-action').value = 'pan';
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
			}, '-', {
				text : 'Cerrar',
				tooltip : 'Cerrar esta pesta&ntilde;a.',
				iconCls : 'icon-16-dialog-close',
				handler : function() {
					this.closeTab();
				},
				scope : this
			}, '-', '<span id="' + this.mapname + '-scale">&nbsp;</span>']
		});

		//var tree = this.getLayersTree();
		//var findForm = this.getSearchForm(this);

		var tabs = new Ext.TabPanel({
			id: this.mapname + '-east',
			region : 'east',
			collapsible : false,
			collapsed : false,
			border : false,
			split : true,
			width : 210,
			minTabWidth : 100,
			tabWidth : 130,
			enableTabScroll : true,
			layoutOnTabChange : true,
			activeTab : 0,
			defaults : {
				autoScroll : true
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
		this.items = [navigation, tabs];
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
			store : ds,
			width : 120,
			fieldLabel : 'Capa',
			displayField : 'text',
			valueField : 'text',
			typeAhead : true,
			editable : false,
			allowBlank : false,
			mode : Ext.isIE ? 'local' : 'remote',
			triggerAction : 'all',
			emptyText : '---',
			selectOnFocus : true
		});

		var frm = new Ext.FormPanel({
			id : panel.mapname + '-search-frm',
			iconCls : 'icon-16-edit-find',
			title : "Busqueda R&aacute;pida",
			formId : 'frm-search',
			labelWidth : 50,
			height : 30,
			frame : true,
			border : false,
			monitorValid : true,
			labelAlign : 'right',
			defaultType : 'textfield',
			items : [cmb, {
				fieldLabel : 'Buscar',
				width : 120,
				id : 'search-text',
				name : 'search-value',
				allowBlank : false
			}],
			buttons : [{
				id : panel.mapname + '-search-btn',
				iconCls : 'icon-16-edit-find',
				formBind : true,
				handler : function() {

					var layer = cmb.getValue();
					var search = Ext.getCmp('search-text').getValue();
					this.showSearchResults(layer, search);
				},
				scope : panel
			}]
		});

		return frm;
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

	addListeners : function() {
		var tree = this.getLayersTree();
		var findForm = this.getSearchForm(this);
		
		var east = Ext.getCmp(this.mapname + '-east');
		east.add(tree);
		east.add(findForm);
		east.setActiveTab(tree);

		var tip = Ext.getCmp(this.mapname + '-ttip');
		if (tip) {
			tip.destroy();
		}
		new Ext.ToolTip({
			id : this.mapname + '-ttip',
			target : this.mapname + '-img',
			title : '-',
			width : 100,
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

		view.addListener('click', this.onMouseClick, this);
		view.addListener('mousewheel', this.onMouseWheel, this);
		view.addListener('mousemove', this.onMouseMove, this);

		var dd = new Ext.dd.DD(img);
		dd.onMouseDown = this.onMouseDown.createDelegate(this);
		dd.onDrag = this.onDragImage.createDelegate(this);
		dd.startDrag = this.onStartDrag.createDelegate(this);
	},

	onStartDrag : function(x, y) {
		var img = this.getImage();
		this.imageBegin = [img.getLeft(), img.getTop()];
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
			xajax.$(this.mapname + '-action').value = 'zoom-in';
		} else {
			xajax.$(this.mapname + '-action').value = 'zoom-out';
		}
		var img = this.getImage();
		this.imageBegin = [img.getLeft(), img.getTop()];
		this.onMouseClick();
		xajax.$(this.mapname + '-action').value = 'pan';
		e.stopEvent();
	},

	onMouseClick : function() {
		var img = Ext.get(this.mapname + '-img');
		img.setOpacity(0, true);
		this.maskPanel(true);

		var js = "var img = Ext.get('" + this.mapname + "-img');" + "img.setX("
				+ this.imageBegin[0] + "); img.setY(" + this.imageBegin[1]
				+ ");";

		xajax_AppHome.exec({
			action : this.classUI + '.doAction',
			enableajax : true,
			args : [{
				action : xajax.$(this.mapname + '-action').value,
				extent : xajax.$(this.mapname + '-ex').value,
				x : xajax.$(this.mapname + '-x').value,
				y : xajax.$(this.mapname + '-y').value
			}],
			jscallback : js
		});
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
			title : "Leyenda",
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

		tree.on('checkchange', function(node, checked) {
			var img = Ext.get(this.mapname + '-img');
			img.setOpacity(0, true);
			this.maskPanel(true);

			xajax_AppHome.exec({
				action : this.classUI + '.doAction',
				enableajax : true,
				args : [{
					layer : node.text,
					status : checked
				}]
			});
		}, this);

		return tree;
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

	getContainer : function() {
		var container = Ext.getCmp('center-panel').getActiveTab();
		return container;
	},

	closeTab : function() {
		Ext.getCmp('center-panel').remove(this.getContainer(), true);
	}
});

Ext.reg('mspanel', Ext.MapPanel);