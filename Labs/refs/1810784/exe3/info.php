<!DOCTYPE html>
<?php
    $user = $_GET['user'];
?>
<html>
    <body>
        <h1>Sign in by User <?php echo $user; ?><h1>
        <input type="button" value="logout" onclick="window.location.href = 'logout.php'">
    </body>
</html>