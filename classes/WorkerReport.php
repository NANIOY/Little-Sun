<?php
include_once (__DIR__ . '/../bootstrap.php');

class WorkerReport
{

    private $db;

    const SETTINGS = [
        'db' => [
            'user'      => 'root',
            'password'  => '',
            'host'      => '127.0.0.1',
            'port'      => 3306,
            'database'  => 'littlesun',
        ],
    ];

    public function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s',
            self::SETTINGS['db']['host'],
            self::SETTINGS['db']['port'],
            self::SETTINGS['db']['database']
        );
        $username = self::SETTINGS['db']['user'];
        $password = self::SETTINGS['db']['password'];

        try {
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function getFilteredWorkers($locations = [], $tasks = [], $workers = [], $overtime = false)
    {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($locations)) {
            $locationPlaceholders = implode(',', array_fill(0, count($locations), '?'));
            $query .= " AND location_id IN ($locationPlaceholders)";
            $params = array_merge($params, $locations);
        }

        if (!empty($tasks)) {
            $taskPlaceholders = implode(',', array_fill(0, count($tasks), '?'));
            $query .= " AND tasks_id IN ($taskPlaceholders)";
            $params = array_merge($params, $tasks);
        }

        if (!empty($workers)) {
            $workerPlaceholders = implode(',', array_fill(0, count($workers), '?'));
            $query .= " AND id IN ($workerPlaceholders)";
            $params = array_merge($params, $workers);
        }

        if ($overtime) {
            $query .= " AND overtime = 1";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
