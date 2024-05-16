<?php
include_once(__DIR__ . '/../bootstrap.php');

class WorkerReport
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getFilteredUsers($locations = [], $tasks = [], $users = [], $overtime = false)
    {
        $query = "SELECT users.* FROM users
                  LEFT JOIN task_user_assignment ON users.id = task_user_assignment.user_id
                  LEFT JOIN tasks ON task_user_assignment.task_id = tasks.id
                  WHERE 1=1";
        $params = [];

        if (!empty($locations)) {
            $locationPlaceholders = implode(',', array_fill(0, count($locations), '?'));
            $query .= " AND users.location_id IN ($locationPlaceholders)";
            $params = array_merge($params, $locations);
        }

        if (!empty($tasks)) {
            $taskPlaceholders = implode(',', array_fill(0, count($tasks), '?'));
            $query .= " AND tasks.id IN ($taskPlaceholders)";
            $params = array_merge($params, $tasks);
        }

        if (!empty($users)) {
            $userPlaceholders = implode(',', array_fill(0, count($users), '?'));
            $query .= " AND users.id IN ($userPlaceholders)";
            $params = array_merge($params, $users);
        }

        if ($overtime) {
            $query .= " AND users.overtime = 1";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
