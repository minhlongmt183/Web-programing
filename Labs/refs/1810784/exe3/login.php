<!DOCTYPE html>
<html>
    <head>
        <title>
            Lab09
        </title>
        <script>
            function setCookie(cname, cvalue, cpass, cpvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                var expires = "expires=" + d.toGMTString();
                document.cookie = cname + "=" + cvalue + ";" + cpass + "=" + cpvalue + ";" + expires + ";path=/";
                console.log(document.cookie);
            }

            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }

            function checkCookie() {
                var user = getCookie("username");
                var pass = getCookie("password");
                if (user != "") {
                    alert("Welcome again " + user);
                    window.location.href = "info.php?user=" + user;
                }
            }

            function signin(){
                var user = document.getElementById("username").value;
                var pass = document.getElementById("password").value;
                if (user != "" && user != null && pass != "" && pass != null) {
                    setCookie("username", user, "password", pass, 30);
                }
                window.location.href = "info.php?user=" + user;
            }
        </script>
    </head>
    <body onload="checkCookie()" id="body">
        USERNAME: <input type='text' id='username'/> <br> 
        PASSWORD: <input type='text' id='password'/> <br> 
        <input type='button' value='Sign in' onclick="signin()"/>
    </body>
</html>