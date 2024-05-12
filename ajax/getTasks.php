<?php
include_once (__DIR__ . '/../classes/Task.php');

if (!isset($_GET['worker_id'])) {
    http_response_code(400);
    echo json_encode(array('error' => 'Worker ID is required'));
    exit();
}

$workerId = $_GET['worker_id'];

$tasks = Task::getTasksByWorkerId($workerId);

header('Content-Type: application/json');
echo json_encode($tasks);