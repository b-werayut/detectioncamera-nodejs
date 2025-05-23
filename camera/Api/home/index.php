<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';

class ChatServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Message received: $msg\n";

        // ส่งข้อความไปยังทุกคนที่เชื่อมต่ออยู่
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection closed ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\WebSocket\WsServer(new ChatServer()),
    8080
);
echo "WebSocket Server running on port 8080...\n";
$server->run();
?>
