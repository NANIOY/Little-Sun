<?php
include_once (__DIR__ . '/../bootstrap.php');

class ScheduleManager
{
    public static function assignSchedule($userId, $taskId, $startTime, $endTime, $date, $locationId)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('INSERT INTO schedules (start_time, end_time, date, task_id, location_id) 
                                     VALUES (:startTime, :endTime, :date, :taskId, :locationId)');
        $statement->bindValue(':startTime', $startTime);
        $statement->bindValue(':endTime', $endTime);
        $statement->bindValue(':date', $date);
        $statement->bindValue(':taskId', $taskId);
        $statement->bindValue(':locationId', $locationId);
        $statement->execute();

        $scheduleId = $conn->lastInsertId();

        $statement = $conn->prepare('INSERT INTO schedule_user_assigned (user_id, schedule_id) 
                                     VALUES (:userId, :scheduleId)');
        $statement->bindValue(':userId', $userId);
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();

        return ['success' => true, 'message' => 'Schedule assigned successfully.'];
    }

}
