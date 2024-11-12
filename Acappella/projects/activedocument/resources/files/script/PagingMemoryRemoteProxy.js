/**
 * PagingMemoryRemoteProxy
 * @author Kevin Darlington
 * 
 * This plugin is based on PagingMemoryProxy by Ing. Ido Sebastiaan Bas van Oostveen : http://extjs.com/forum/showthread.php?t=11652
 * 
 * This plugin has all the features of the original PagingMemoryProxy, but adds a few things:
 * 1. Caching of data so the reader doesn't have to transform the data into records with each call.
 * 2. Ability to grab the entire data set from the server, and be able to page it.
 *      This is the main reason for this plugin. I had a very special case where paging using
 *      the server alone was slow and not what I wanted. You can supply an url parameter to the
 *      PagingMemoryRemoteProxy, and on the first call to load it will grab the data from the server.
 *      It will be paged based on the parameters you send in as normal. Any consecutive calls to
 *      load will load it from the cache. You must call reset() in order for it to fetch the data
 *      from the server again.
 *
 * If you want this to act exactly like PagingMemoryProxy, then call it like this:
 *   new PagingMemoryRemoteProxy({data: yourdata});
 *   
 * @changelog:
 *
 * @version 1.8
 * @date 19-June-2008
 *   - Fixed a sorting problem where if two items were equal, it would return -1 or 1
 *     instead of returning 0 like it should. This resulted in items being sorted differently
 *     each time.
 * 
 * @version 1.7
 * @date 5-June-2008
 *   - Added the loadRemoteParam option.
 * 
 * @version 1.6
 * @date 2-June-2008
 *   - Fixed a bug where it would get the range + 1
 * 
 * @version 1.5
 * @date 20-May-2008
 *   - Added the beforeload and load events.
 * 
 * @version 1.4
 * @date 16-May-2008
 *   - Fixed a sorting issue that would sort the entire list even if the query hasn't changed, reducing
 *     performance greatly on large data sets.
 *   - Added the sortOnNewQueryOnly option
 * 
 * @version 1.3
 * @date 7-May-2008
 *   - Some significant changes to this version. Read the examples to learn how to use it.
 *   - Renamed to PagingMemoryRemoteProxy from PagingMemoryHttpProxy
 *   - Added a paramModify option that allows you do exchange params locally and remotely.
 *   - Added a matchLocation option that allows you to say where the query will be located.
 *   - Fixed a sorting issue returning a boolean instead of an integer (bug from PagingMemoryProxy)
 * 
 * @version  1.2
 * @date  6-May-2008
 *   - Added the ability to reload from source if the query is new.
 *   - Fixed some bugs.
 * 
 * @version    1.1
 * @date       5-May-2008
 *   - Added the ability to filter results based on the query.
 * @version	1.0
 * @date	28-April-2008
 *
 */

Ext.namespace("Ext.ux");
Ext.namespace("Ext.ux.data");

//====================================
/* Fixes for IE/Opera old javascript versions */
if (!Array.prototype.map) {
  Array.prototype.map = function(fun) {
  	var len = this.length;
  	if (typeof fun != "function") {
  	    throw new TypeError();
  	}
  	var res = new Array(len);
  	var thisp = arguments[1];
  	for (var i = 0; i < len; i++){
  	  if (i in this) {
  		  res[i] = fun.call(thisp, this[i], i, this);
  	  }
  	}
    return res;
  };
}

//====================================
if (!Array.prototype.filter) {
  Array.prototype.filter = function(fun /*, thisp*/) {
    var len = this.length;
    if (typeof fun != "function")
      throw new TypeError();

    var res = new Array();
    var thisp = arguments[1];
    for (var i = 0; i < len; i++) {
      if (i in this){
        var val = this[i]; // in case fun mutates this
        if (fun.call(thisp, val, i, this))
          res.push(val);
      }
    }
    return res;
  };
}

//====================================
Ext.ux.clone = function(o, deep) {
  if (!o) return o;
  
  var objectClone = new o.constructor();
  for (var property in o) 
    if (!deep) 
      objectClone[property] = o[property];
    else if (typeof o[property] == 'object') 
      objectClone[property] = Ext.ux.clone(o[property], deep);
    else 
      objectClone[property] = o[property];
  return objectClone;
}

//====================================
Ext.ux.data.PagingMemoryRemoteProxy = function(config) {
  var self = this;
  
  //If data is passed, the matchLocation default is 'local'. It's 'remote' otherwise.
  if (config.data) {
    Ext.applyIf(config, {
      matchLocation: 'local'
    });
  } else {
    Ext.applyIf(config, {
      matchLocation: 'remote'
    });
  }
    
  Ext.apply(self, config);
	Ext.ux.data.PagingMemoryRemoteProxy.superclass.constructor.call(self);  
  
  if (!self.remoteProxy && self.url) {
    //If the url is undefined, firefox will throw an exception.
    self.remoteProxy = new Ext.data.HttpProxy({url: self.url});
  }
  
  //You can pass in your own snapshot in case you want to do some preprocessing.
  if (self.snapshot)
    self.data = []; //just so the code works
  else
    self.snapshot = new Ext.util.MixedCollection(false);
  self.current = self.snapshot;
};

//------------------------------------
Ext.extend(Ext.ux.data.PagingMemoryRemoteProxy, Ext.data.DataProxy, {
  /**
   * @cfg {Array} data
   * The multi-dimensional array of data.
   */
  data: undefined,
  /**
   * @cfg {String} customFilter
   * A custom filter that can be applied to the result.
   */
  customFilter: null,
  /**
   * @cfg {String} url
   * The url to get the data from. If you set remoteProxy yourself, then this value is ignored.
   */
  url: undefined,
  /**
   * @cfg {Object} remoteProxy
   * The proxy that retrieves the data via http or other means. Can either be Ext.data.HttpProxy, or Ext.data.ScriptTagProxy.
   */
  remoteProxy: undefined,
  
  /**
   * @cfg {String} matchParam
   * Allows you to specify a parameter that will filter the results based on the value of that parameter. For example, if you
   * have this option set to 'query' and you call store.load({params: {query: 'hello'}});, then it will only return results
   * that have 'hello' in it. Be careful with this. If you have the server filter results by a query, and you have this enabled,
   * then it will filter the results locally as well. To prevent this, use paramModify to move the query param from local
   * to remote.
   */
  matchParam: 'query',
  
  /**
   * @cfg {Object} matchColumn
   * A number or string that specifies the column which to match against.
   */
  matchColumn: 0,
  
  /**
   * @cfg {String} matchLocation
   * Specifies where the matchParam is located: in the local params or remoteParams. Possible values: 'local', 'remote'.
   * You must take into consideration any moves you've done inside paramModify. Defaults to 'local' if data is passed,
   * and 'remote' otherwise.
   */
  matchLocation: 'remote',
  
  /**
   * @cfg {Boolean} reloadOnNewQuery
   * If true, if the query is new then it will reget the data remotely (considering the data parameter was not specified
   * in the creation of PagingMemoryRemoteProxy). If the query is the same as the last query done, it will not reget
   * remotely. Defaults to false.
   */
  reloadOnNewQuery: false,
  
  /**
   * @cfg {Boolean} sortOnNewQueryOnly
   * If true, the data set will only be sorted if it's a new query. This is useful for a paging combobox that is being sorted
   * and has a large dataset. Setting this to true will prevent the entire dataset from being sorted each time you change
   * pages. Defaults to false.
   */
  sortOnNewQueryOnly: false,
  
  /**
   * @cfg {Array} paramModify
   * An array of objects that describes how to modify params. For example, you can exchange parameters from local proxy params 
   * to remote params and vise versa. This is useful in case you are using extjs components that only send in params, but you 
   * want these params to be sent to the backend as well.
   * 
   * Object format:<br/>
   * <ul>
   * <li><b>param</b> : String<p class="sub-desc">The parameter to move or copy.</p></li>
   * <li><b>toParam</b>: String<p class="sub-desc">The parameter to move or copy to. If blank, will use what you specified for param.</p></li>
   * <li><b>from</b> : String<p class="sub-desc">Where to copy/move from. Can be 'remote' or 'local'.</p></li>
   * <li><b>to</b> : String<p class="sub-desc">Where to copy/move to. Can be 'remote' or 'local'.</p></li>
   * <li><b>operation</b> : String<p class="sub-desc">Can either be 'move' or 'copy'.</p></li>
   * </ul>
   * 
   * Example:
   * <pre>
   *  
   * var proxy = new Ext.ux.data.PagingMemoryRemoteProxy({
   *   url: 'backend.data.html',
   *   paramModify: [
   *     {param: 'query', from: 'local', to: 'remote', operation: 'move'}
   *   ]
   * });
   * </pre>
   * This will move the query parameter from "params" to "remoteParams".
   */
  paramModify: [],
  
  /**
   * @cfg {String} loadRemoteParam
   * 
   * If this is set, then data will only be fetched remotely if this parameter is present when load() is called. This is useful
   * if you do not want PagingMemoryRemoteProxy to fetch data remotely when someone clicks the column header in a grid before
   * the grid contains any data.
   * 
   * Example:
   * loadRemoteParam: 'remote'
   * 
   * This load will only use data that the proxy has already fetched. If there is no data, it will do nothing.
   * load({params: {start: 0, limit: 10}})
   * 
   * This load will fetch the data remotely because the 'remote' param is present.
   * load({params: {start: 0, limit: 10, remote: true}})
   * 
   */
  loadRemoteParam: undefined,
  
  //private
  lastQuery: null,
  
  //====================================
  //Pass in remoteParams as an arg for the parameters that need to be sent to the server itself.
  //Anything inside params will not be sent remotely unless you specify the option remoteParamsAsParams to be true.
  load: function(params, reader, callback, scope, arg) {
    var self = this;
		Ext.encode(arg);
    if (self.fireEvent("beforeload", self, params) !== false) {            
      var o = {
        params: params || {},
        remoteParams: arg.remoteParams || {},
        callback: callback,
        scope: scope,
        arg: arg
      };
      
      self.modifyParams(o.params, o.remoteParams); 
      
      //Get the query and reset the filter based on the last query.
      var query = self.getQuery(o);
      if (!query || query != self.lastQuery)
        self.clearFilter(); 
         
  		try {
        //If reloadOnNewQuery is set to true, the query is different from the last query, then
        //clear the snapshot so we can reget the data.
        if (self.reloadOnNewQuery && query != self.lastQuery) {
          self.snapshot.clear();
        }
        
        if (self.snapshot.getCount() > 0) {
          self.loadResult(o, true);
        } else if (self.data) {
          self.loadRecords(reader.readRecords(self.data));
          self.loadResult(o, true);
        } else {
          if (self.loadRemoteParam !== undefined && o.params[self.loadRemoteParam] == undefined) {
            self.fireEvent("load", self, o, o.arg);
		        o.callback.call(o.scope, undefined, o.arg, true);
            return;
          }
          
          self.remoteProxy.load(o.remoteParams, reader, function(result, arg, success) {
            if (success) {
              self.loadRecords(result);            
            }
            self.loadResult(o, success);
          }, self, arg);
        }
  		} catch (e) {
  			self.fireEvent("loadexception", self, arg, null, e);
  			callback.call(scope, null, arg, false);
  			return;
  		}
    } else {
      callback.call(scope||self, null, arg, false);
    }
	},
  
  //====================================
  //private
  loadRecords: function(o) {
    var r = o.records, t = o.totalRecords || r.length;
    for (var i = 0, len = r.length; i < len; i++){
      r[i].join(this);
    }
    this.snapshot.addAll(r);
  },
  
  //====================================
  //private
  createFilterFn: function(property, value, anyMatch, caseSensitive) {
    if (Ext.isEmpty(value, false)) {
      return false;
    }
    value = this.snapshot.createValueMatcher(value, anyMatch, caseSensitive);
    return function(r){
      return value.test(r.data[property]);
    };
  },
  
  //====================================
  //private
  modifyParams: function(local, remote) {
    var self = this;
    
    if (!self.paramModify || !self.paramModify.length) return;
       
    Ext.each(self.paramModify, function(x) {
      if (x.operation == 'copy' || x.operation == 'move') {
        var from = x.from == 'local' ? local : remote;
        var to = x.to == 'local' ? local : remote;
        
        to[x.toParam || x.param] = from[x.param];
        
        if (x.operation == 'move') 
          delete from[x.param];
      } else if (x.operation == 'add') {
        var to = x.to == 'local' ? local : remote;
        
        to[x.param] = x.value;
      }
    });
  },
  
  //====================================
  //private
  getQuery: function(o) {
    var self = this;
    
    var query = undefined;
    
    if (self.matchLocation == 'local') {
      query = o.params[self.matchParam] || undefined;
    } else {
      query = o.remoteParams[self.matchParam] || undefined;
    }
    
    return query;
  },
    
  //====================================
  reset: function() {
    this.snapshot.clear();  
  },
  
  //====================================
  clearFilter: function() {
    this.current = this.snapshot;
  },
  
  //====================================
  //private
  loadResult: function(o, success) {
    var self = this;
    
    if (!success) {
      o.callback.call(o.scope, null, o.arg, false);
      return;
    }
      
    var a = self.current;
    var query = self.getQuery(o);
    
    // query match filtering
    // The reason why we don't do this code if the match location is remote and there is a query is because
    // someone would only do this if they wanted the backend to filter the results based on the query. It would
    // be nonsense to refilter the data locally once the backend has already filtered it.
    if (!(self.matchLocation == 'remote' && query)) {        
      if (self.matchParam && self.matchParam != '' && query && query != self.lastQuery) {
        var fn = self.createFilterFn(String(self.matchColumn), query, true, false);
        a = a.filterBy(fn);
        self.current = a.clone();
      }
    }
           
    // filtering
    if (this.customFilter != null) {
			a = a.filterBy(this.customFilter);
		} else if (o.params.filter !== undefined) {
      var att = o.params.filterCol || 0;
			a = a.filter(att, o.params.filter);
		}
		
		// sorting
    // Don't sort again if the last query is the same as this query.
    if (!self.sortOnNewQueryOnly || (query !== self.lastQuery || self.lastQuery === null)) {
      if (o.params.sort !== undefined) {
        var fn = function(r1, r2){
          if (r1 < r2) 
            return -1;
          if (r1 > r2) 
            return 1;
          return 0;
        };
        a.sort(String(o.params.dir).toUpperCase(), function(first, second) {
          var v = 0;
          if (typeof(a) == "object") {
            v = fn(first.data[o.params.sort], second.data[o.params.sort]);
          } else {
            v = fn(first, second);
          }
          return v;
        });
      }
    }
    
    var endIndex = o.params.start+o.params.limit-1;
    if (endIndex < 0)
      endIndex = 0;
    
    var result = {};
		// paging (use undefined cause start can also be 0 (thus false))
		if (o.params.start !== undefined && o.params.limit !== undefined) {           
			result.records = a.getRange(o.params.start, endIndex);
		} else {
      result.records = a.getRange();
    }
    
    result.totalRecords = a.getCount();
    
    self.lastQuery = query;
		
    self.fireEvent("load", self, o, o.arg);
		o.callback.call(o.scope, result, o.arg, true);
  }
});