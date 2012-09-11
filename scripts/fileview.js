var pathToFileView = "http://www.pwnict.co.uk/fileView/displayFolder.php";

// set on document load
window.onload = function(){
loadDisplayFolder("fileView", pathToFileView);
};


function getParameterByName(name){
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function loadDisplayFolder(div, URL){
	var divCheck = document.getElementById(div);
	if (divCheck != null){
		var xmlhttp;
		divCheck.innerHTML = divCheck.innerHTML + "<br><p>Please wait..</p>";
		if (window.XMLHttpRequest) {
		
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {
			// code for IE6, IE5
		
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
					dealWithResponse(div, xmlhttp.responseText);
			}
		
		}
		// get id and pid. this allows the user to jump back to this position!
		var id = getParameterByName("id");
		var pid = getParameterByName("pid");
		if(id != "") {
			URL+= ("?id=" + id);
			if (pid != ""){
				URL += ("&pid=" + pid);
			}
		} 
		
		xmlhttp.open("GET",convertAbsoluteURL(URL),true);
		xmlhttp.send();
		
	} else {
		alert("You must have a DIV tag with an ID of fileView to use fileView");	
	}
	
}

function convertAbsoluteURL(URL){
	var current = window.location.href;
	
	// split both
	// remove any query strings first!
	var currentArray = current.split("?");
	var urlArray = URL.split("?");
	// store query string for later!
	var queryString = "";
	if (urlArray.length >1){
		queryString = "?" + urlArray[1];
	}
	
	currentArray = currentArray[0].split("/");
	urlArray = urlArray[0].split("/");
	
	var a=0;
	
	while (currentArray[a] == urlArray[a]){
		a = a +1;
		if (a >= currentArray.length || a >= urlArray.length){
			alert ("error");
		}
	}
	
	var rel = "";
	for (var i =a; i<currentArray.length; i++){
		rel += "../";
	}
	
	// then construct the rest of the link
	for (var i =a; i<urlArray.length; i++){
		rel += urlArray[i];
		rel += "/";
	}
	rel = rel.substr(rel, rel.length -1);
	
	return rel + queryString;
}

function sethtml(div,content) 
{ 
    var search = content; 
    var script; 
          
    while( script = search.match(/(<script[^>]+javascript[^>]+>\s*(<!--)?)/i)) 
    { 
      search = search.substr(search.indexOf(RegExp.$1) + RegExp.$1.length); 
       
      if (!(endscript = search.match(/((-->)?\s*<\/script>)/))) break; 
       
      block = search.substr(0, search.indexOf(RegExp.$1)); 
      search = search.substring(block.length + RegExp.$1.length); 
       
      var oScript = document.createElement('script'); 
      oScript.text = block; 
      document.getElementsByTagName("head").item(0).appendChild(oScript); 
    } 
    
    document.getElementById(div).innerHTML=content; 
}

function dealWithResponse(divID, resp){
	
	var div = document.getElementById(divID);
	if (div != null){
		
		sethtml(divID, resp);
		//div.innerHTML = resp;
	} else {
		alert("You must have a DIV tag with an ID of fileView to use fileView");	
	}
}