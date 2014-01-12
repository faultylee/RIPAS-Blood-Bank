//V3.01.A - http://www.openjs.com/scripts/jx/
function jx(url,callback,isJson){
	if (window.XMLHttpRequest){
		http = new XMLHttpRequest();
	} else {
		try {http=new ActiveXObject("Msxml2.XMLHTTP")	} catch(e){
		try {http=new ActiveXObject("Microsoft.XMLHTTP")} catch(e){}}
	}
	if(!http||!url||!callback) return;
	http.open(
		"GET",
		url+((url.indexOf("?")+1)?"&":"?")+"ief="+new Date().getTime(), //Cache problem in IE.
		true
	);
	http.onreadystatechange=function(){
		if(http.readyState == 4){
			if(http.status == 200){
				var result = "";
				if(http.responseText)
					result=http.responseText;
				if(isJson)
					result = eval('('+result.replace(/[\n\r]/g,"")+')'); 
				callback(result);
			} else if(error)
				error(http.status);
		}
	}
	http.send(null);
}