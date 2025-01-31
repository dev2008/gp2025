<!DOCTYPE html>
<html>
<head>

<?php

if (isset($_GET['asm'])) {
$host=$_SERVER['HTTP_HOST'];
echo "<title>$host</title></head>";
echo "<body>";
echo "<h1>Welcome to WebRoot!</h1>";
phpinfo();
echo "</body>";
    
} else {
    echo "<p>Hello, I think you are lost</p>";
    
}

echo "</header>";
echo "</div>";

?>
</body>
</html>
