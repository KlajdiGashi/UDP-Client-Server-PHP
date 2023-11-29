<?php
// Server config
$serverHost = '127.0.0.1';
$serverPort = 12345;

// Password
$adminPassword = 'passwordis123';

// Create socket
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, $serverHost, $serverPort);

echo "UDP Server listening on $serverHost:$serverPort\n";

while (true) {
    // Leximi i te dhenave nga klienti
    socket_recvfrom($socket, $buffer, 1024, 0, $clientAddress, $clientPort);
    echo "Received from $clientAddress:$clientPort: $buffer\n";

    // processimi i te dhenave
    $response = processRequest($buffer, $adminPassword);

    // dergimi i nje response per klientin
    socket_sendto($socket, $response, strlen($response), 0, $clientAddress, $clientPort);

    if ($buffer === 'exit') {
        break;
    }
}
socket_close($socket);



function processRequest($request, $adminPassword, &$clientData, &$clients)
{
    $response = "Invalid command";
    $requestParts = explode(" ", $request);
    
    if (isset($requestParts[0]) && isset($requestParts[1])) {
        $command = $requestParts[0];
        $password = $requestParts[1];

        // perdorimii i switch case per kontrollimin e komandave
        switch ($command) {
            case '/password':
               
                break;

            case '/write':
                // Check if all necessary arguments are provided
              
                break;
            
            case '/read':
               
                break;

            case '/listen':
              
                break;

            case 'execute':
                
                break;
        }
    }

    return $response;
}
