window.onload=function(){
	$("diary").onclick=function(){
		var bodyStr=$("body").value;
		var headStr=$("head").value;
		if(bodyStr.indexOf(headStr)==-1){
			alert("This diary body doesn't include the diary head.");
			return false;
		}
	}
}