/*
 * @author DiMarcello
 */

Ext.tree.TreeStoreLoader = function(config) {

	this.baseParams = {};
	this.test = 'testing';
	this.requestMethod = "POST";
	this.storeLoaded = false;
	
	Ext.apply(this, config);
	
	this.dataFields = this.store.reader.recordType.prototype.fields.keys;
	
	if (!this.dataFields)
		this.dataFields = ['id', 'text', 'children'];
		
	for (var i = 0, l = this.dataFields.length; i < l; i++) {
		if (typeof this.dataFields[i] == "string") {
			this.dataFields[i] = {
				name : this.dataFields[i]
			};
		}
	}
	
	this.addEvents("beforeload", "load", "loadexception", "update");
	Ext.tree.TreeStoreLoader.superclass.constructor.call(this);
};

Ext.extend(Ext.tree.TreeStoreLoader, Ext.tree.TreeLoader, {
	
	addChildren : function(parent) {
		this.store.each(function(rec) {
			parent.appendChild(this.createChild(rec));
		}, this);
		if (parent.attributes.expanded === true)
			parent.expand();
	},

	createChild : function(rec) {
		var attr = {};
		if (this.baseAttrs)
			Ext.applyIf(attr, this.baseAttrs);
			
		if (this.applyLoader !== false)
			attr.loader = this;
			
		for (var i = 0, f = this.dataFields; i < f.length; i++) {
			if (f[i].mapping !== undefined && f[i].mapping !== null) {
				if (typeof f[i].mapping == 'function') {
					attr[f[i].name] = f[i].mapping(rec);
				} else {
					attr[f[i].name] = rec.get(f[i].mapping);
				}
			} else {
				attr[f[i].name] = rec.get(f[i].name);
			}
		}
		return new Ext.tree.TreeNode(attr);
	},

	load : function(node, callback) {
		if (!!this.storeLoaded) {
			if (this.clearOnLoad) {
				while (node.firstChild) {
					node.removeChild(node.firstChild);
				}
			}
			this.addChildren(node);
			if (typeof callback == "function")
				callback();
		} else {
			this.bindStore(this.store, node, callback, true);
		}
	},

	bindStore : function(store, node, callback, initial) {
		if (this.store && !initial
				&& this.fireEvent("beforeload", this, node, callback) !== false) {
			this.store.un('load', this.handleResponse.createDelegate(this, [
					node, callback], 0));
			this.store.un('update', this.handleUpdate.createDelegate(this,
					[node], 0));
			this.store.un('loadexception', this.handleFailure.createDelegate(
					this, [node, callback], 0));
			if (!store) {
				this.store = null;
			}
			this.storeLoaded = false;
		}

		if (!store && initial) {
			this.store = new Ext.data.JsonStore({
				url : this.dataUrl,
				root : this.dataRoot,
				fields : ['text']
			});
		}
		if (store) {
			this.store = Ext.StoreMgr.lookup(store);
			this.store.on('load', this.handleResponse.createDelegate(this, [
					node, callback], 0));
			this.store.on('update', this.handleUpdate.createDelegate(this,
					[node], 0));
			this.store.on('loadexception', this.handleFailure.createDelegate(
					this, [node, callback], 0));
			this.storeLoaded = this.store.loaded;
		}
		if (!this.storeLoaded) {
			this.store.load();
			this.storeLoaded = true;
		} else {
			this.load.call(this, node, callback);
		}
	},

	processResponse : function(node, callback) {
		try {
			this.load.call(this, node, callback);
		} catch (e) {
			this.handleFailure(node, callback);
		}
	},

	handleUpdate : function(node, s, r, operation) {
		if (operation == Ext.data.Record.COMMIT) {
			this.transId = false;
			/*
			 * TODO var old = node.findChild('id', r.get('id')); if(old){ var nn =
			 * this.createChild(r); node.replaceChild(nn, old); nn.highlight(); }
			 */
			this.processResponse(node, null);
			this.fireEvent("update", this, node);
		}
	},

	handleResponse : function(node, callback) {
		this.transId = false;
		this.processResponse(node, callback);
		this.fireEvent("load", this, node);
	},

	handleFailure : function(node, callback) {
		this.transId = false;
		this.fireEvent("loadexception", this, node);
		if (typeof callback == "function")
			callback(this, node);
	}

});