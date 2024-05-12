<?php
include_once (__DIR__ . '/../bootstrap.php');

class ScheduleManager
{
    public static function assignSchedule($userId, $taskId, $startTime, $endTime, $date, $locationId) {
        $conn = Db::getInstance();
        $statement = $conn->prepare('INSERT INTO schedules (start_time, end_time, task_id, location_id) VALUES (:startTime, :endTime, :taskId, :locationId)');
        $statement->bindValue(':startTime', $startTime);
        $statement->bindValue(':endTime', $endTime);
        $statement->bindValue(':taskId', $taskId);
        $statement->bindValue(':locationId', $locationId);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'User is already scheduled or has time off during this period.'];
        }

        $statement = $conn->prepare('INSERT INTO schedules (start_time, end_time, task_id, location_id) VALUES (:startTime, :endTime, :taskId, :locationId)');
        $statement->bindValue(':startTime', $startTime);
        $statement->bindValue(':endTime', $endTime);
        $statement->bindValue(':taskId', $taskId);
        $statement->bindValue(':locationId', $locationId);
        $statement->execute();
        $scheduleId = $conn->lastInsertId();

        $statement = $conn->prepare('INSERT INTO schedule_user_assigned (user_id, schedule_id) VALUES (:userId, :scheduleId)');
        $statement->bindValue(':userId', $userId);
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();

        return ['success' => true, 'message' => 'Schedule assigned successfully.'];
    }
}
