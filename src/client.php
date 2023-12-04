<?php
$serverHost = '127.0.0.1';
$serverPort = 50001;

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

if ($socket === false) {
    echo "Failed to create socket: " . socket_strerror(socket_last_error()) . PHP_EOL;
    exit;
}
while (true) {
    echo "Enter message (type 'exit' to quit): ";
    $input = rtrim(fgets(STDIN), "\r\n");

    socket_sendto($socket, $input, strlen($input), 0, $serverHost, $serverPort);

    $bytesReceived = socket_recvfrom($socket, $response, 1024, 0, $serverHost, $serverPort);

    if ($bytesReceived === false) {
        echo "Error receiving data: " . socket_strerror(socket_last_error()) . PHP_EOL;
    } else {
        echo "Server response: $response" . PHP_EOL;
    }

    if ($input === 'exit') {
        break;
    }
}

socket_close($socket);
?>
