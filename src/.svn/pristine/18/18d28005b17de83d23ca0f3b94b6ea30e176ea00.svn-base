// JavaScript Document
function getXMLHttp()
{
  var xmlHttp
  try
  {
    //Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
  }
  catch(e)
  {
    //Internet Explorer
    try
    {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch(e)
    {
      try
      {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e)
      {
        alert("Your browser does not support AJAX!")
        return false;
      }
    }
  }
  return xmlHttp;
}
// prepare rearrange provider list
function loginRadiusRearrangeProviderList(elem){
	var ul = document.getElementById('sortable');
	if(elem.checked){
		var listItem = document.createElement('li');
		listItem.setAttribute('id', 'loginRadiusLI'+elem.value);
		listItem.setAttribute('title', elem.value);
		listItem.setAttribute('class', 'lrshare_iconsprite32 lrshare_'+elem.value);
		// append hidden field
		var provider = document.createElement('input');
		provider.setAttribute('type', 'hidden');
		provider.setAttribute('name', 'rearrange_settings[]');
		provider.setAttribute('value', elem.value);
		listItem.appendChild(provider);
		ul.appendChild(listItem);
	}else{
		ul.removeChild(document.getElementById('loginRadiusLI'+elem.value));
	}
}
// check provider more then 9 select
function loginRadiusSharingLimit(elem){
	var provider = $("#shareprovider").find(":checkbox");
	var checkCount = 0;
	for(var i = 0; i < provider.length; i++){
		if(provider[i].checked){
			// count checked providers
			checkCount++;
			if(checkCount >= 10){
				elem.checked = false;
				$("#loginRadiusSharingLimit").show('slow');
				setTimeout(function() {
					$("#loginRadiusSharingLimit").hide('slow');
				}, 5000);
				//document.getElementById('loginRadiusSharingLimit').style.display = 'block';
				return;
			}
		}
	}
}
function Makevertivisible() {
  document.getElementById('sharevertical').style.display="block";
  document.getElementById('sharehorizontal').style.display="none";
  document.getElementById('arrow').style.cssText = "position:absolute; border-bottom:8px solid #ffffff; border-right:8px solid transparent; border-left:8px solid transparent; margin:-18px 0 0 70px;";
}
function Makehorivisible() {
  document.getElementById('sharehorizontal').style.display="block";
  document.getElementById('sharevertical').style.display="none";
  document.getElementById('arrow').style.cssText = "position:absolute; border-bottom:8px solid #ffffff; border-right:8px solid transparent; border-left:8px solid transparent; margin:-18px 0 0 2px;";
   document.getElementById('mymodel').style.color = "#ffffff";
}
function Makecvertivisible() {
  document.getElementById('countervertical').style.display="block";
  document.getElementById('counterhorizontal').style.display="none";
  document.getElementById('carrow').style.cssText = "position:absolute; border-bottom:8px solid #ffffff; border-right:8px solid transparent; border-left:8px solid transparent; margin:-18px 0 0 70px;";
}

function Makechorivisible() {
  document.getElementById('counterhorizontal').style.display="block";
  document.getElementById('countervertical').style.display="none";
  document.getElementById('carrow').style.cssText = "position:absolute; border-bottom:8px solid #ffffff; border-right:8px solid transparent; border-left:8px solid transparent; margin:-18px 0 0 2px;";
 }
function MakeRequest()
{
   document.getElementById("ajaxDiv").innerHTML = '<div id ="loading">Contacting API - please wait ...</div>'; 	
   var connection_url = document.getElementById('connection_url').value;
   var apikey = document.getElementById('apikey').value;
   if (apikey == '') {
	   document.getElementById('ajaxDiv').innerHTML = '<div id="Error">please enter api key</div>';
	   return false;
   }
   var apisecret = document.getElementById('apisecret').value;
   if (apisecret == '') {
	   document.getElementById('ajaxDiv').innerHTML = '<div id="Error">please enter api secret</div>';
	   return false;
   }
   if (document.getElementById('curl').checked) {
	   var api_request = 'curl';
   }
   else {
	   var api_request = 'fsockopen';   
   }
   var xmlHttp = getXMLHttp();
   xmlHttp.onreadystatechange = function()
  {
    if(xmlHttp.readyState == 4){
		if (xmlHttp.status==200 ){
          document.getElementById("ajaxDiv").innerHTML=xmlHttp.responseText
        }
        else{
          document.getElementById("ajaxDiv").innerHTML='<div id="Error">An error has occured making the request</div>';
        }     
    }
  }
  xmlHttp.open("GET", connection_url+"administrator/components/com_socialloginandsocialshare/views/socialloginandsocialshare/checkapi.php?apikey=" + apikey +"&apisecret="+apisecret+"&api_request="+api_request, true);
  xmlHttp.send(null);
}