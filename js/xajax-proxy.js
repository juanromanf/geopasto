/*
 * Xajax Proxy. by Juank.
 */
Ext.data.XajaxProxy = function(config) {

	Ext.apply(this, config);
	Ext.data.XajaxProxy.superclass.constructor.call(this);
};

Ext.extend(Ext.data.XajaxProxy, Ext.data.DataProxy, {

	load : function(params, reader, callback, scope, arg) {
		/*
		 * Fire 'beforeload' event.
		 */
		if (this.fireEvent("beforeload", this, params) !== false) {
			/*
			 * Execute the xajax funcion.
			 */
			xajax.request({
				xjxcls : this.xjxcls,
				xjxmthd : this.xjxmthd
			}, {
				responseProcessor : this.oResponseProcessor,
				oEventParams : {
					proxy : this,
					params : params,
					callback : callback,
					scope : scope,
					arg : arg,
					reader : reader
				},
				parameters : [params]
			});
		} else {

			callback.call(scope || this, null, arg, false);
		}

	},
	oResponseProcessor : function(oRequest) {

		var oRet = '';
		if (oRequest.request.responseXML) {

			var responseXML = oRequest.request.responseXML;
			if (responseXML.documentElement) {

				var child = responseXML.documentElement.firstChild;
				while (child) {
					// catch xajax return value.
					if ('xjxrv' == child.nodeName) {
						oRet = child.firstChild.data;
					}
					child = child.nextSibling;
				}
			}
		}
		// alert(oRet);
		var jsonRet = Ext.util.JSON.decode(oRet);
		var proxy = oRequest.oEventParams.proxy;
		var result, ds;

		try {
			result = oRequest.oEventParams.reader.readRecords(jsonRet);
			ds = oRequest.oEventParams.scope;

			proxy.fireEvent("load", this, oRequest,
					oRequest.oEventParams.callback);
			ds.fireEvent("load", ds, result.records, oRequest.oEventParams);

			oRequest.oEventParams.callback.call(oRequest.oEventParams.scope,
					result, oRequest.oEventParams.arg, true);

		} catch (e) {
			proxy.fireEvent("loadexception", this, oRequest,
					oRequest.oEventParams.callback, e);

			oRequest.oEventParams.callback.call(oRequest.oEventParams.scope,
					null, oRequest.oEventParams.arg, false);
			return;
		}
	},

	update : function(dataSet) {

	},

	updateResponse : function(dataSet) {

	}

});