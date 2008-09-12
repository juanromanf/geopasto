

Ext.tree.TreeStoreLoader = function(config) {

	/**
	 * name of the parameter in the HTTP request (QueryString or POST data)
	 */
	this.nodeParamName = "node";

	// required to be set to "true" (or non empty string) by the parent class to
	// work properly
	this.dataUrl = true;

	Ext.apply(this, config);

	this.dataFields = this.store.reader.recordType.prototype.fields.keys;

	if (!this.dataFields) {
		this.dataFields = ['id', 'text', 'children'];
	}
	for (var i = 0, l = this.dataFields.length; i < l; i++) {
		if (typeof this.dataFields[i] == "string") {
			this.dataFields[i] = {
				name : this.dataFields[i]
			};
		}
	}

	if (!this.store) {
		this.store = new Ext.data.JsonStore({
			url : this.dataUrl,
			root : this.dataRoot,
			fields : ['text']
		});
	}

	Ext.tree.TreeStoreLoader.superclass.constructor.call(this);
};

Ext.extend(Ext.tree.TreeStoreLoader, Ext.tree.TreeLoader, {

	requestData : function(node, callback) {
		if (this.fireEvent("beforeload", this, node, callback) !== false) {
			this.store.purgeListeners();
			this.store.on('load', this.handleResponse.createDelegate(this, [
					node, callback], 0));
			this.store.on('loadexception', this.handleFailure.createDelegate(
					this, [node, callback], 0));
			
			var load = (typeof(this.store.autoLoad) == 'undefined') ? true : false;
			if (load) {
				this.store.load({
					params : this.getParams(node),
					callback : callback
				});
			}
		} else {
			if (typeof callback == "function") {
				callback();
			}
		}
	},

	getStore : function() {
		return this.store;
	},

	getParams : function(node) {
		var params = {};
		var bp = this.baseParams;
		for (var key in bp) {
			if (typeof bp[key] != "function") {
				params[key] = bp[key];
			}
		}
		params[this.nodeParamName] = node.id;
		return params;
	},

	processResponse : function(node, callback) {
		try {
			if (this.clearOnLoad) {
				while (node.firstChild) {
					node.removeChild(node.firstChild);
				}
			}
			this.addChildren(node);

			if (typeof callback == "function")
				callback();
		} catch (e) {
			this.handleFailure(node, callback);
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
		if (typeof callback == "function") {
			callback(this, node);
		}
	},

	addChildren : function(parent) {
		this.store.each(function(rec) {
			parent.appendChild(this.createChild(rec));
		}, this);
		if (parent.attributes.expanded === true) {
			parent.expand();
		}
	},

	createChild : function(rec) {
		var attr = {};
		for (var i = 0, f = this.dataFields; i < f.length; i++) {
			attr[f[i].name] = Ext.isEmpty(f[i].mapping)
					? rec.get(f[i].name)
					: ((typeof f[i].mapping == 'function')
							? f[i].mapping(rec)
							: rec.get(f[i].mapping));
		}
		return this.createNode(attr);
	}

});