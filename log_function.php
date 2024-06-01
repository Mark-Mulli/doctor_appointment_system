<?php
function logger($log) {
    if (!file_exists('log.txt')) {
        file_put_contents('log.txt','');
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $timezone = new DateTimeZone("Africa/Nairobi");
    $now = new DateTime('now', $timezone);
    $time = $now->format('Y-m-d H:i:s');

    $contents = file_get_contents('log.txt');
    $contents .= "$ip\t$time\t$log\r";

    file_put_contents('log.txt', $contents);

}
