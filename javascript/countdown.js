var END_TOKEN = "||";
var calls = 0;
var xhReq = createXMLHttpRequest();
window.onload = function() {
  setInterval(periodicXHReqCheck, 100);
  xhReq.open("GET", "countdown.phtml?start=3", true);
  xhReq.onreadystatechange = function() {
    if (xhReq.readyState==4) { /* alert("done!");  */ }
  }
  xhReq.send(null);
}

function periodicXHReqCheck() {
  var fullResponse = util.trim(xhReq.responseText);
  var responsePatt = /^(.*@END@)*(.*)@END@.*$/;
  if (fullResponse.match(responsePatt)) { // At least one full response so far
    var mostRecentDigit = fullResponse.replace(responsePatt, "$2");
    $("response").innerHTML = mostRecentDigit;
  }
}

function createXMLHttpRequest() {
  try { return new ActiveXObject("Msxml2.XMLHTTP");    } catch(e) {}
  try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) {}
  try { return new XMLHttpRequest();                   } catch(e) {}
  alert("XMLHttpRequest not supported");
  return null;
}