/*
 * AppHome Javascript File.
 */

Ext.BLANK_IMAGE_URL = './img/s.gif'; // Url imagen transparente.

Ext.override(Ext.Element, {
	mask : function(msg, msgCls, maskCls) {
		if (this.getStyle("position") == "static") {
			this.setStyle("position", "relative");
		}
		if (this._maskMsg) {
			this._maskMsg.remove();
		}
		if (this._mask) {
			this._mask.remove();
		}

		this._mask = Ext.DomHelper.append(this.dom, {
			cls : maskCls || "ext-el-mask"
		}, true);

		this.addClass("x-masked");

		this._mask.setDisplayed(true);

		if (typeof msg == 'string') {
			// *** FIX : create element hidden

			this._maskMsg = Ext.DomHelper.append(this.dom, {
				style : "visibility:hidden",
				cls : "ext-el-mask-msg",
				cn : {
					tag : 'div'
				}
			}, true);
			var mm = this._maskMsg;
			mm.dom.className = msgCls
					? "ext-el-mask-msg " + msgCls
					: "ext-el-mask-msg";
			mm.dom.firstChild.innerHTML = msg;
			(function() {
				mm.setDisplayed(true);
				mm.center(this);
				mm.setVisible(true);
			}).defer(20, this); // *** FIX : defer things a bit, so the mask
								// sizes over the el properly before centering
		}
		if (Ext.isIE && !(Ext.isIE7 && Ext.isStrict)
				&& this.getStyle('height') == 'auto') { // ie will not expand
														// full height
														// automatically
			this._mask.setSize(this.dom.clientWidth, this.getHeight());
		}
		return this._mask;
	}
});

var AppHome = function() {

	Ext.QuickTips.init();
	// do NOT access DOM from here; elements don't exist yet

	// private variables

	// private functions

	function buildPanelTree(p) {
		var ctl = Ext.ComponentMgr.get(p.id + "-tree");

		/*
		 * Si el arbol del menu aun no ha sido constriudo.
		 */
		if (typeof(ctl) == 'undefined') {

			var r = new Ext.data.Record.create([{
				name : 'id'
			}, {
				name : 'text'
			}, {
				name : 'iconCls'
			}, {
				name : 'action'
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
					action : 'AppMenu.getMenuTree',
					returnvalue : true,
					args : [p.id]
				}
			});

			var myloader = new Ext.tree.TreeStoreLoader({
				store : ds
			});

			var tree = new Ext.tree.TreePanel({
				id : p.id + "-tree",
				useArrows : true,
				autoScroll : true,
				border : false,
				animate : true,
				rootVisible : false,
				// height : 500,
				width : 200,
				containerScroll : true,
				listeners : {
					click : function(n) {
						// Add new Tab to Center Panel
						var center = Ext.getCmp('center-panel');
						var tab = Ext.getCmp(n.attributes.id + '-tab');

						if (typeof(tab) == 'undefined') {
							center.add({
								id : n.attributes.id + '-tab',
								title : n.attributes.text,
								closable : true,
								autoScroll : true,
								iconCls : n.attributes.iconCls
							}).show();

							// Ext.MessageBox.alert('action',
							// n.attributes.action);

							var xaction = new String(n.attributes.action)
									.split('.');

							xajax_AppHome.exec({
								action : n.attributes.action,
								jscallback : xaction[0] + '.init();'
							});

						} else {
							center.setActiveTab(tab);
						}
					}// click end
				}// listeners end
			});

			// set the root node
			var root = new Ext.tree.AsyncTreeNode({
				expanded : true,
				loader : myloader
			});
			tree.setRootNode(root);

			p.add(tree);
			p.doLayout();
		}
	}

	function _getAccordionRecord() {
		var _record = new Ext.data.Record.create([{
			name : 'id'
		}, {
			name : 'title'
		}, {
			name : 'iconCls'
		}, {
			name : 'collapsed'
		}, {
			name : 'border'
		}]);

		return _record;
	}

	function buildAccordion() {
		var xstore = new Ext.data.Store({
			autoLoad : true,
			reader : new Ext.data.JsonReader({}, _getAccordionRecord()),
			proxy : new Ext.data.XajaxProxy({
				xjxcls : 'AppHome',
				xjxmthd : 'exec'
			}),
			baseParams : {
				action : 'AppMenu.getModules',
				returnvalue : true
			},
			listeners : {
				load : function(st, rs, op) {

					for (var index = 0; index < rs.length; index++) {
						Ext.getCmp('west-panel').remove(rs[index].get('id'));

						var p = new Ext.Panel({
							id : rs[index].get('id'),
							title : rs[index].get('title'),
							iconCls : rs[index].get('iconCls'),
							border : rs[index].get('border'),
							collapsed : rs[index].get('collapsed')
						});
						p.on('beforeexpand', buildPanelTree);
						Ext.getCmp('west-panel').add(p);
					}
					Ext.getCmp('west-panel').doLayout();
				}
			}
		});
	}

	// public space

	return {
		// public properties, e.g. strings to translate
		// public methods
		init : function() {
			// AppHome.init();

			var MainWindow = new Ext.Viewport({
				id : 'main-view',
				layout : "border",
				items : [{
					region : 'south',
					id : 'south-panel',
					split : true,
					collapsible : true,
					collapsed : true,
					height : 150,
					minSize : 100,
					maxSize : 200,
					xtype : 'tabpanel',
					minTabWidth : 110,
					tabWidth : 130,
					enableTabScroll : true,
					activeTab : 0,
					defaults : {
						autoScroll : true
					},
					items : [{
						title : 'Mensajes',
						id : 'debug-tab',
						html : '<div id="debug-div">&nbsp;</div>',
						iconCls : 'icon-16-format-list-unordered'
					}]
				}, {
					region : "west",
					id : 'west-panel',
					title : "Modulos",
					iconCls : 'icon-16-emblem-generic',
					collapsed : true,
					collapsible : true,
					width : 200,
					margins : '0 0 0 5',
					layout : 'accordion',
					layoutConfig : {
						animate : true
					},
					listeners : {
						render : function(p) {
							buildAccordion();
						}
					}
				}, {
					region : "center",
					id : 'center-panel',
					xtype : 'tabpanel',
					enableTabScroll : true,
					minTabWidth : 120,
					layoutOnTabChange : true,
					tabWidth : 200,
					resizeTabs : true, // turn on tab resizing
					activeTab : 0,
					defaults : {
						autoScroll : true
					},
					items : [{
						title : 'Bienvenid@',
						id : 'welcome-tab',
						iconCls : 'icon-16-home',
						html : '<div id = "welcome-div"></div>'
					}]
				}]
			});

			xajax_AppHome.exec({
				action : 'AppHome.DisplayWelcome',
				target : 'welcome-div'
			});
		} // init end

	};// return end
}();