<?php
$content1 = file_get_contents('resources/views/livewire/public/auction/live.blade.php');
$open1 = substr_count($content1, '<div');
$close1 = substr_count($content1, '</div');
echo "live.blade.php - Open: $open1, Close: $close1\n";

$content2 = file_get_contents('resources/views/livewire/team/auction/bidding.blade.php');
$open2 = substr_count($content2, '<div');
$close2 = substr_count($content2, '</div');
echo "bidding.blade.php - Open: $open2, Close: $close2\n";
