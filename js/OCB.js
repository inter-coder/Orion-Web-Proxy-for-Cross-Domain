(function ($) {
    $.OCB=function(p){
			/*
			/  p.url - Specifies the URL to send the request to. Default is the current page
			/  p.ocbIP - Specifies the Orion Server IP to send the request to. Default is the current server (localhost) 
			/  p.ocbPort - Specifies the Orion Server Port, Default is the 1026
			/  p.async - A Boolean value indicating whether the request should be handled asynchronous or not. Default is true
			/  p.cache - A Boolean value indicating whether the browser should cache the requested pages. Default is true
			/  p.parm - Server param to deside what to do with data
			/  p.data - Object of data ex.
			/  p.success - A function to be run when the request succeeds
			/  p.error - A function to run if the request fails.
			/  p.timeout - The local timeout (in milliseconds) for the request, Default is the 10000ms (10s)
			*/
			if(p==undefined)return false;
			$.ajax({
			  url: p.url==undefined?"":p.url,
			  timeout:p.timeout==undefined?10000:p.timeout,
			  type:p.type==undefined?"POST":p.type,
			  cache: p.cache==undefined?false:p.cache,
			  param:p.param==undefined?false:p.param,
			  data:{
			  	"data":JSON.stringify(p.data),
			  	"ocbIP":p.ocbIP==undefined?"localhost":p.ocbIP, 
			  	"ocbPort":p.ocbPort==undefined?1026:p.ocbPort,
			  },
			  success:p.success==""?false:p.success,
			  error:p.error==""?false:p.error
			});
		};
}(jQuery));