<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lab4</title>
	<script type="text/javascript">

		function setCookie(cname, cvalue, exdays){
			const d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			let expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function getCookie(cname){
			let name = cname + "=";
			let decoded_cookie = decodeURIComponent(document.cookie);
			let ca = decoded_cookie.split(';');
			for (let i = 0; i < ca.length; i++){
				let c = ca[i];
				while (c.charAt(0) == ' '){
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		function checkCookie(){
			let user = getCookie("username");
			if (user != ""){
				alert("Cookiee existed! Hello " + user);
			}
			else
			{
				user = prompt("Please enter your name:","");
				if (user != "" && user != null){
					setCookie("username", user, 60);
				}
			}
		}
		function deleteCookie(){
			var d = new Date();
			d.setDate(d.getDate());
			var expires = "expires=" + d.toUTCString();
			document.cookie = "username=" + ";" + expires + ";path=/";
			// document.cookie = "username=; expires=Mon, 01 Jan 1900 00:00:00 UTC; path=/;";
		}

		
	</script>
</head>
<body onload="checkCookie()">
	<input type="button" value="delete" onclick="deleteCookie()">

</body>
</html>