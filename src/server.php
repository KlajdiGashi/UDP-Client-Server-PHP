<?php

$serverHost = '127.0.0.1';
$serverPort = 50001;

$adminPassword = 'passwordis123';

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, $serverHost, $serverPort);

echo "UDP Server listening on $serverHost:$serverPort\n";

$clients = [];
$loggedInUsers = [];  // Array to track successfully logged-in users
while (true) {
    socket_recvfrom($socket, $buffer, 1024, 0, $clientAddress, $clientPort);
    echo "Received from $clientAddress:$clientPort: $buffer\n";

    $response = processRequest($buffer, $adminPassword, $clients, $loggedInUsers);

    if ($clientPort !== null) {
        socket_sendto($socket, $response, strlen($response), 0, $clientAddress, $clientPort);
    } else {
        echo "Error: Client port is null.\n";
    }

    if ($buffer === 'exit') {
        break;
    }
}
socket_close($socket);

function processRequest($request, $adminPassword, &$clientData, &$loggedInUsers)
{
    $response = "Invalid command";
    echo "Raw request: $request\n";

    $parts = explode(" ", $request);

    $command = isset($parts[0]) ? $parts[0] : '';
    $password = isset($parts[1]) ? $parts[1] : '';

    $content = implode(" ", array_slice($parts, 2));

    if (!empty($command) && !empty($password)) {
        switch ($command) {
            case '/password':
                // kontrollimi i password
                if ($password === $adminPassword) {
                    $loggedInUsers[] = $clientData['address'];
                    $response = "Administrator login successful";
                } else {
                    $response = "Administrator login failed";
                }
                break;

            case '/write':
                if (isset($parts[2]) && isset($parts[3])) {
                    if (in_array($clientData['address'], $loggedInUsers)) {
                        $fileName = 'output.txt';
                        $fileContent = $content;  

                        
                        if (file_put_contents($fileName, $fileContent, LOCK_EX) !== false) {
                            
                            fclose(fopen($fileName, 'a'));
                            $response = "File '$fileName' written successfully.";
                            
                            $clientData['writtenContent'] = $fileContent;
                        } else {
                            $response = "Failed to write to file '$fileName'.";
                        }
                    } else {
                        $response = "You don't have permission to execute /write. Log in first.";
                    }
                } else {
                    $response = "Invalid arguments for /write command. Usage: /write password content_to_write";
                }
                break;

            case '/read':
                
                $fileName = 'output.txt';

                if (file_exists($fileName)) {
                    $fileContent = file_get_contents($fileName);
                    $response = "File content:\n$fileContent";
                } else {
                    $response = "File '$fileName' not found. Use '/write' to create and write to a file.";
                }
                break;

            case '/execute':
                
                if (in_array($clientData['address'], $loggedInUsers) && $password === $adminPassword) {
                    $fileName = 'output.txt';

                    
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

                case '/listen':
                    // Return a list of connected clients
                    $response = "Connected clients:\n";
                    foreach ($clients as $clientAddress => $clientData) {
                        $response .= "$clientAddress\n";
                    }
                    break;

            case '/help':
                
                $response = "Available commands:\n";
                $response .= "/password <password> - Log in as administrator\n";
                $response .= "/write <password> <content> - Write content to file\n";
                $response .= "/read - Read content from file\n";
                $response .= "/execute <password> - Delete the file (admin only)\n";
                $response .= "/listen - Start listening for incoming data\n";
                $response .= "/help - Display help information\n";
                break;

            default:
                $response = "Unknown command: $command";
                break;
        }
    }

    return $response;
}
