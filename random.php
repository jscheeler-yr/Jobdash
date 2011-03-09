<?php
function genRandomString() {
    $length = 8;
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters .= 'abcdefghijklmnopqrstuvwxyz!@#';
    $string = "";
    for ($p = 0; $p <= $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

echo genRandomString();

echo "<br>" . strlen("jessica.scheeler@yr.com");

$string = "JOBDASH-CHI-password";
echo "<br>" . md5($string);

echo "<br>--------- Making time -------------";
$datetime = date("Y-m-d H:i:s");
echo "<br>$datetime";

?>