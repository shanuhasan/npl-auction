<?php
$f = 'd:/xampp/htdocs/npl/ipl-auction/resources/views/home.blade.php';
$c = file_get_contents($f);
$pos = strpos($c, '</x-ipl-layout>');
if ($pos !== false) {
    $c = substr($c, 0, $pos + 15);
    file_put_contents($f, $c . PHP_EOL);
    echo "Cleaned!";
}
