# Computer Networks Project 02
### General Info
Sockets are used for interprocess communication. Interprocess communication is generally based on client-server model. In this case, client-server are the applications that interact with each other. Interaction between client and server requires a connection. 
Socket programming is responsible for establishing that connection between applications to interact.

### Implementation 
   * PHP Server
   1. Set variables such as "host" and "port"
   2. Create Socket
   3. Bind the socket to port and host
   4. Start listening to the socket
   5. Accept incoming connection
   6. Read the message from the Client socket
   7. Send message to the client socket
   8. Close the socket

  * PHP Client
  1. Set variables such as "host" and "port"
  2. Create Socket
  3. Connect to the server
  4. Write to server socket
  5. Read the response from the server
  6. Close the socket.

  * Protocol
       * User Datagram Protocol ([UDP](https://en.wikipedia.org/wiki/User_Datagram_Protocol)) is a network protocol that operates at the transport layer of the              Internet Protocol (IP) suite.
      
       * How does UDP work ?

            ![Alt text](/img/udp.PNG)  [^1] 
          

### Technologies
The implementation of the program was made using [PHP](https://www.php.net/) Server Scripting Language.


### Contributors 
- [Ilirjana Suka](https://github.com/IlirjanaSuka)

- [Jeta Syla](https://github.com/Jeta-Syla)

- [Klajdi Gashi](https://github.com/KlajdiGashi)

- [Kleda Gashi](https://github.com/kledagashi)

### References
[^1] [Image](https://www.cloudflare.com/learning/ddos/glossary/user-datagram-protocol-udp/)
