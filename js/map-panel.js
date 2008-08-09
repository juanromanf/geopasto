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
			data : [['640 x 480', '640x480'],
					['800 x 600', '800x600']]
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
			allowBlank : false,
			mode : 'local',
			triggerAction : 'all',
			emptyText : '...',
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

		var tree = this.getLayersTree();

		var p1 = new Ext.Panel({
			region : 'east',
			title : 'Informacion',
			collapsible : true,
			collapsed : false,
			autoScroll : true,
			width : 210,
			items : tree
		});

		var p2 = new Ext.Panel({
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
		this.items = [p1, p2];
		this.height = this.getContainer().getEl().getHeight() - 2;

		Ext.MapPanel.superclass.initComponent.call(this);
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

	addListeners : function() {
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

		var view = Ext.get(this.mapname + '-div');
		var img = Ext.get(this.mapname + '-img');
		img.on('load', function() {
			img.setOpacity(1, true);
		});
		this.imageBegin = [img.getLeft(), img.getTop()];

		var w = (img.getWidth() + 10) + "px";
		var h = (img.getHeight() + 10) + "px";

		view.setStyle('width', w);
		view.setStyle('height', h);
		view.setStyle('overflow', 'hidden');
		view.setStyle('margin', '5px auto 5px auto');

		view.addListener('click', this.onMouseClick, this);
		view.addListener('mousewheel', this.wheel, this);
		view.addListener('mousemove', this.onMouseMove, this);

		if (!Ext.isIE) {
			view.addListener('mousedown', this.onMouseDown, this);
			view.addListener('mouseup', this.onMouseUp, this);
		}
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

	onMouseDown : function(e) {

		if (this.getActiveAction() == 'pan') {
			var img = Ext.get(this.mapname + '-img');

			var x = img.getX();
			var y = img.getY();

			this.ddImage = true;
			this.dragBegin = [x, y];
			this.mouseBegin = [e.getPageX(), e.getPageY()];

			e.stopEvent();
		}
	},

	onMouseUp : function(e) {

		if (this.ddImage) {
			var img = Ext.get(this.mapname + '-img');
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
		}

		this.ddImage = false;
		this.dragBegin = null;
		this.mouseBegin = null;
		e.stopEvent();
	},

	onMouseMove : function(e) {
		var img = Ext.get(this.mapname + '-img');
		var x = e.getPageX() - img.getLeft();
		var y = e.getPageY() - img.getTop();

		if (this.ddImage) {
			img.setX(this.dragBegin[0] - this.mouseBegin[0] + e.getPageX());
			img.setY(this.dragBegin[1] - this.mouseBegin[1] + e.getPageY());

			xajax.$(this.mapname + '-action').value = 'pan';
			Ext.getCmp(this.mapname + '-btn-pan').toggle(true);
		}
		xajax.$(this.mapname + '-x').value = x;
		xajax.$(this.mapname + '-y').value = y;

		var tip = Ext.getCmp(this.mapname + '-ttip');
		tip.setTitle("<center>(" + Math.round(x) + ", " + Math.round(y)
				+ ")</center>");
	},

	/**
	 * Event handler for mouse wheel event.
	 */
	wheel : function(e) {
		var delta = e.getWheelDelta();
		if (delta < 0) {
			xajax.$(this.mapname + '-action').value = 'zoom-in';
		} else {
			xajax.$(this.mapname + '-action').value = 'zoom-out';
		}
		this.onMouseClick();
		xajax.$(this.mapname + '-action').value = 'pan';
		e.stopEvent();
	},

	getLayersTree : function() {
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

		var tree = new Ext.tree.TreePanel({
			id : this.mapname + "-tree",
			useArrows : true,
			autoScroll : true,
			border : false,
			animate : true,
			rootVisible : true,
			// height : 500,
			width : 200,
			containerScroll : true
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

		// set the root node
		var root = new Ext.tree.AsyncTreeNode({
			text : 'Capas',
			expanded : true,
			loader : myloader
		});
		tree.setRootNode(root);

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