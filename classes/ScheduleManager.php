<?php
include_once (__DIR__ . '/../bootstrap.php');

class ScheduleManager
{
    public static function assignSchedule($userId, $taskId, $startTime, $endTime, $date, $locationId)
    {
        if (self::isUserOnTimeOff($userId, $date)) {
            return ['success' => false, 'message' => '<span style="color: red;">User is on time off on the requested date.</span>'];
        }

        if (!self::isUserAvailableDuringTimeOff($userId, $startTime, $endTime, $date)) {
            return ['success' => false, 'message' => '<span style="color: red;">User is not available during the requested time period.</span>'];
        }

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

    private static function isUserAvailableDuringTimeOff($userId, $startTime, $endTime, $date)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('SELECT * FROM time_off WHERE user_id = :userId 
                                AND ((startDate <= :endTime AND endDate >= :startTime) OR DATE(startDate) = :date)');
        $statement->bindValue(':userId', $userId);
        $statement->bindValue(':startTime', $startTime);
        $statement->bindValue(':endTime', $endTime);
        $statement->bindValue(':date', $date);
        $statement->execute();
        $timeOffRecords = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($timeOffRecords as $timeOffRecord) {
            $timeOffStartTime = strtotime($timeOffRecord['startDate']);
            $timeOffEndTime = strtotime($timeOffRecord['endDate']);

            if (($timeOffStartTime <= $endTime) && ($timeOffEndTime >= $startTime)) {
                return false;
            }
        }

        return true;
    }

    private static function isUserOnTimeOff($userId, $date)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('SELECT * FROM time_off WHERE user_id = :userId 
                                AND :date BETWEEN startDate AND endDate');
        $statement->bindValue(':userId', $userId);
        $statement->bindValue(':date', $date);
        $statement->execute();
        $timeOffRecord = $statement->fetch(PDO::FETCH_ASSOC);

        return $timeOffRecord !== false;
    }

    public static function getScheduleById($scheduleId)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('
        SELECT s.*, sua.user_id 
        FROM schedules s 
        LEFT JOIN schedule_user_assigned sua ON s.id = sua.schedule_id 
        WHERE s.id = :scheduleId
    ');
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }


    public static function deleteSchedule($scheduleId)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('DELETE FROM schedules WHERE id = :scheduleId');
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();

        return ['success' => true, 'message' => 'Schedule deleted successfully.'];
    }

    public static function updateSchedule($scheduleId, $userId, $taskId, $startTime, $endTime, $date, $locationId)
    {
        if (self::isUserOnTimeOff($userId, $date)) {
            return ['success' => false, 'message' => '<span style="color: red;">User is on time off on the requested date.</span>'];
        }

        if (!self::isUserAvailableDuringTimeOff($userId, $startTime, $endTime, $date)) {
            return ['success' => false, 'message' => '<span style="color: red;">User is not available during the requested time period.</span>'];
        }

        $conn = Db::getInstance();

        $statement = $conn->prepare('UPDATE schedules SET start_time = :startTime, end_time = :endTime, date = :date, task_id = :taskId, location_id = :locationId WHERE id = :scheduleId');
        $statement->bindValue(':startTime', $startTime);
        $statement->bindValue(':endTime', $endTime);
        $statement->bindValue(':date', $date);
        $statement->bindValue(':taskId', $taskId);
        $statement->bindValue(':locationId', $locationId);
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();

        $statement = $conn->prepare('UPDATE schedule_user_assigned SET user_id = :userId WHERE schedule_id = :scheduleId');
        $statement->bindValue(':userId', $userId);
        $statement->bindValue(':scheduleId', $scheduleId);
        $statement->execute();

        return ['success' => true, 'message' => 'Schedule updated successfully.'];
    }
}