<!DOCTYPE html>
<html>
    <head>
        <script>
            function Delete(cname, cpass){
                var d = new Date();
                d.setTime(d.getTime() + (30*24*60*60*1000));
                var expires = "expires=" + d.toGMTString();
                document.cookie = cname + "=" + ";" + cpass + "=" + ";" + expires + ";path=/";
                window.location.href = "login.php";
            }    
        </script>
    </head>
    <body onload="Delete('username', 'password');">
    </body>
</html>