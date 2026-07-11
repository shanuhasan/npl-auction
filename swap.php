<?php
$content = file_get_contents('d:/xampp/htdocs/npl/ipl-auction/resources/views/livewire/public/auction/live.blade.php');
preg_match('/(<!-- LEFT COLUMN: Teams and Squads -->.*?)(<!-- RIGHT COLUMN: Active Auction -->.*?)(    <\/div>\s+<!-- Alpine Script -->)/s', $content, $matches);
if(count($matches) === 4) {
    $new_content = str_replace($matches[0], str_replace('RIGHT COLUMN', 'LEFT COLUMN', $matches[2]) . str_replace('LEFT COLUMN', 'RIGHT COLUMN', str_replace(' order-last', '', $matches[1])) . $matches[3], $content);
    file_put_contents('d:/xampp/htdocs/npl/ipl-auction/resources/views/livewire/public/auction/live.blade.php', $new_content);
    echo "Swapped successfully";
} else {
    echo "Failed to match";
}
