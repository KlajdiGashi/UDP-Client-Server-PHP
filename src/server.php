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
               if ($password === $adminPassword) {
                    $response = "Administrator login successful";
                } else {
                    $response = "Administrator login failed";
                }
                break;

            case '/write':

            if (isset($requestParts[2]) && isset($requestParts[3])) {
                    $fileName = 'output.txt';
                    $fileContent = $requestParts[3];
            
                    if (file_put_contents($fileName, $fileContent, LOCK_EX) !== false) {
                        $response = "File '$fileName' written successfully.";

                        $clientData['writtenContent'] = $fileContent;
                    } else {
                        $response = "Failed to write to file '$fileName'.";
                    }
                } else {
                    $response = "Invalid arguments for /write command.";
                }
                break;
            
            case '/read':
               // lexon kontekstin brenda file-it
                $fileName = 'output.txt';

                if (file_exists($fileName)) {
                    $fileContent = file_get_contents($fileName);
                    $response = "File content:\n$fileContent";
                } else {
                    $response = "File '$fileName' not found. Use '/write' to create and write to a file.";
                }
                break;

            case '/listen':
              
                break;

            case 'execute':
                // Kontrollon nese perdoruesi eshte administrator
                if ($password === $adminPassword) {
                    $fileName = 'output.txt';

                    // Fshin file-n ekzistues
                    if (file_exists($fileName)) {
                        unlink($fileName);
                        $response = "Execution privileges granted for administrators. File '$fileName' deleted.";
                    } else {
                        $response = "File '$fileName' not found.";
                    }
                } else {
                    $response = "Invalid password for execute command.";
                }
                break;
        }
    }

    return $response;
}
