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
    $response = processRequest($buffer, $adminPassword, $clients, $clients);

    // dergimi i nje response per klientin
    socket_sendto($socket, $response, strlen($response), 0, $clientAddress, $clientPort);

    if ($buffer === 'exit') {
        break;
    }
}
socket_close($socket);


// funskioni kryesor per procesimin e kerkesave te klientit
function processRequest($request, $adminPassword, &$clientData, &$clients)
{
    $response = "Invalid command";
    echo "Raw request: $request\n";

    // ndarja e kerkesave ne 2 pjese 
    $parts = explode(" ", $request);

    // Marrja e komandes dhe password
    $command = isset($parts[0]) ? $parts[0] : '';
    $password = isset($parts[1]) ? $parts[1] : '';

    // Marrja e pjeses tjeter te inputit
    $content = implode(" ", array_slice($parts, 2));
    if (!empty($command) && !empty($password)) {
        // Use switch case for command processing

        // perdorimi i switch case per kontrollimin e komandave
        switch ($command) {
            case '/password':
                // kontrollimi i password
                if ($password === $adminPassword) {
                    $adminLoggedIn = true;
                    $response = "Administrator login successful";
                } else {
                    $response = "Administrator login failed";
                }
                break;

                case '/write':
                    if (isset($parts[2]) && isset($parts[3])) {
                        $fileName = 'output.txt';
                        $fileContent = $content;  // Using the extracted content variable
                                
                        // Shkrimi ne file
                        if (file_put_contents($fileName, $fileContent, LOCK_EX) !== false) {
                            // e bon lock release qe me u mbyll file dhe me marr inputin e sakt
                            fclose(fopen($fileName, 'a'));
                            $response = "File '$fileName' written successfully.";
                            // Ruajtja e flie-it ne daten e klientit
                            $clientData['writtenContent'] = $fileContent;
                        } else {
                            $response = "Failed to write to file '$fileName'.";
                        }
                    } else {
                        $response = "Invalid arguments for /write command. Usage: /write password content_to_write";
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
            
                case '/listen':
              
                break;

               case '/help':

               break;
        }
    }

    return $response;
}
