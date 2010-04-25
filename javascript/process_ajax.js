function periodicXHReqCheck() {
  var fullResponse = util.trim(xhReq.responseText);
  var responsePatt = /^(.*@END@)*(.*)@END@.*$/; **
  if (fullResponse.match(responsePatt)) { // At least one full response so far
    var mostRecentDigit = fullResponse.replace(responsePatt, "$2");
    $("response").innerHTML = mostRecentDigit;
  }
}

function doHttpRequest() {
  http.open("POST", "http://rathersaur.us/process/register_make.php", true);
  http.onreadystatechange = getHttpRes;

  // Make our POST parameters string…
  var params = "f_option1=" + encodeURI(document.getElementById("f_option1").value)+
	"&f_option2=" + encodeURI(document.getElementById("f_option2").value)+
	"&f_fbuid=" + encodeURI(document.getElementById("f_fbuid").value);

  // Set our POST header correctly…
  http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http.setRequestHeader("Content-length", params.length);
  http.setRequestHeader("Connection", "close");

  // Send the parms data…
  http.send(params);
}

function getHttpRes() {
  if (http.readyState == 4 && http.status == 200) { 
    res = http.responseText;  // These following lines get the response and update the page
    document.getElementById('cell1').innerHTML = res;
    window.location.reload();
  }
}

function getXHTTP() {
  var xhttp;
   try {   // The following "try" blocks get the XMLHTTP object for various browsers…
      xhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e2) {
 		 // This block handles Mozilla/Firefox browsers...
	    try {
	      xhttp = new XMLHttpRequest();
	    } catch (e3) {
	      xhttp = false;
	    }
      }
    }
  return xhttp; // Return the XMLHTTP object
}
var http = getXHTTP(); // This executes when the page first loads