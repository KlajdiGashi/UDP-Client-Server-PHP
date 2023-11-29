<?php
// client config
$serverHost = '127.0.0.1';
$serverPort = 50000;

// create socket
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

if ($socket === false) {
    echo "Failed to create socket: " . socket_strerror(socket_last_error()) . PHP_EOL;
    exit;
}
while (true) {
    // read input from the console
    echo "Enter message (type 'exit' to quit): ";
    $input = rtrim(fgets(STDIN), "\r\n");

    // send input to the server
    socket_sendto($socket, $input, strlen($input), 0, $serverHost, $serverPort);

    // receive and display the response from the server
    socket_recvfrom($socket, $response, 1024, 0, $serverHost, $serverPort);
    echo "Server response: $response" . PHP_EOL;

    if ($input === 'exit') {
        break;
    }
}

// close the socket
socket_close($socket);
?>
