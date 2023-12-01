<?php
// client config
$serverHost = '127.0.0.1';
$serverPort = 50001;

// create socket
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

if ($socket === false) {
    echo "Failed to create socket: " . socket_strerror(socket_last_error()) . PHP_EOL;
    exit;
}

