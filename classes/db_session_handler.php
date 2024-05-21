<?php
class DBSessionHandler implements SessionHandlerInterface
{
    private $pdo;
    private $table;

    public function __construct(PDO $pdo, $table = 'sessions')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $stmt = $this->pdo->prepare("SELECT data FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['data'];
        }

        return '';
    }

    public function write($id, $data)
    {
        $timestamp = time();
        $stmt = $this->pdo->prepare("REPLACE INTO {$this->table} (id, data, timestamp) VALUES (:id, :data, :timestamp)");
        return $stmt->execute(['id' => $id, 'data' => $data, 'timestamp' => $timestamp]);
    }

    public function destroy($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function gc($maxLifetime)
    {
        $old = time() - $maxLifetime;
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE timestamp < :old");
        return $stmt->execute(['old' => $old]);
    }
}

// Set up database connection
$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8';
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Set the custom session handler
$handler = new DBSessionHandler($pdo);
session_set_save_handler($handler, true);

// Start the session
session_start();
?>
