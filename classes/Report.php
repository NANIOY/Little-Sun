<?php
include_once (__DIR__ . '/../bootstrap.php');

class Report
{
    public static function getHoursWorked($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        if ($userId == 'all') {
            $statement = $conn->prepare("SELECT date(clock_in_time) AS date, u.first_name, u.last_name, clock_in_time, clock_out_time, TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time) / 60 AS hours_worked FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE clock_in_time BETWEEN ? AND ?");
            $statement->execute([$startDate, $endDate]);
        } else {
            $statement = $conn->prepare("SELECT date(clock_in_time) AS date, u.first_name, u.last_name, clock_in_time, clock_out_time, TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time) / 60 AS hours_worked FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE a.user_id = ? AND clock_in_time BETWEEN ? AND ?");
            $statement->execute([$userId, $startDate, $endDate]);
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTotalHoursWorked($startDate, $endDate, $userId = null)
    {
        $conn = Db::getInstance();
        if ($userId === null || $userId == 'all') {
            $statement = $conn->prepare("SELECT u.first_name, u.last_name, SUM(TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time)) / 60 AS total_hours_worked FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE clock_in_time BETWEEN ? AND ? GROUP BY u.id");
            $statement->execute([$startDate, $endDate]);
        } else {
            $statement = $conn->prepare("SELECT u.first_name, u.last_name, SUM(TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time)) / 60 AS total_hours_worked FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE a.user_id = ? AND clock_in_time BETWEEN ? AND ? GROUP BY u.id");
            $statement->execute([$userId, $startDate, $endDate]);
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOvertimeHours($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT date(clock_in_time) AS date, u.first_name, u.last_name, clock_in_time, clock_out_time, (TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time) - 8) AS overtime_hours FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE a.user_id = ? AND clock_in_time BETWEEN ? AND ? AND TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time) > 8");
        $statement->execute([$userId, $startDate, $endDate]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSickDays($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        if ($userId === null || $userId == 'all') {
            $statement = $conn->prepare("SELECT s.date AS sick_date, u.first_name, u.last_name, s.reason FROM sick_days s LEFT JOIN users u ON s.user_id = u.id WHERE s.date BETWEEN ? AND ?");
            $statement->execute([$startDate, $endDate]);
        } else {
            $statement = $conn->prepare("SELECT s.date AS sick_date, u.first_name, u.last_name, s.reason FROM sick_days s LEFT JOIN users u ON s.user_id = u.id WHERE s.user_id = ? AND s.date BETWEEN ? AND ?");
            $statement->execute([$userId, $startDate, $endDate]);
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTimeOffRequests($startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT t.*, u.first_name, u.last_name FROM time_off t LEFT JOIN users u ON t.user_id = u.id WHERE t.startDate BETWEEN ? AND ?");
        $statement->execute([$startDate, $endDate]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLatestTimeOffRequests($locationId, $limit = 4)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT t.*, u.first_name, u.last_name FROM time_off t LEFT JOIN users u ON t.user_id = u.id WHERE u.location_id = ? ORDER BY t.startDate DESC LIMIT ?");
        $statement->bindValue(1, $locationId, PDO::PARAM_INT);
        $statement->bindValue(2, $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTodaySchedule($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT s.*, u.first_name, u.last_name, u.profile_img, t.color, t.title AS task_title FROM schedules s LEFT JOIN schedule_user_assigned sua ON s.id = sua.schedule_id LEFT JOIN users u ON sua.user_id = u.id LEFT JOIN tasks t ON s.task_id = t.id WHERE s.location_id = ? AND s.date = CURDATE()");
        $statement->execute([$locationId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getClockedInWorkers($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT a.*, u.first_name, u.last_name FROM attendance a LEFT JOIN users u ON a.user_id = u.id WHERE a.clock_out_time IS NULL AND u.location_id = ?");
        $statement->execute([$locationId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMissedHoursDueToSickness($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        if ($userId) {
            $statement = $conn->prepare("
            SELECT 
                u.first_name, u.last_name,
                SUM(TIMESTAMPDIFF(HOUR, s.start_time, s.end_time)) AS missed_hours
            FROM 
                schedules s
            LEFT JOIN 
                schedule_user_assigned sua ON s.id = sua.schedule_id
            LEFT JOIN 
                sick_days sd ON sua.user_id = sd.user_id AND s.date = sd.date
            LEFT JOIN 
                users u ON sua.user_id = u.id
            WHERE 
                sua.user_id = :userId AND s.date BETWEEN :startDate AND :endDate AND sd.date IS NOT NULL
            GROUP BY 
                sua.user_id, u.first_name, u.last_name
        ");
            $statement->execute([
                ':userId' => $userId,
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
        } else {
            $statement = $conn->prepare("
            SELECT 
                u.first_name, u.last_name,
                SUM(TIMESTAMPDIFF(HOUR, s.start_time, s.end_time)) AS missed_hours
            FROM 
                schedules s
            LEFT JOIN 
                schedule_user_assigned sua ON s.id = sua.schedule_id
            LEFT JOIN 
                sick_days sd ON sua.user_id = sd.user_id AND s.date = sd.date
            LEFT JOIN 
                users u ON sua.user_id = u.id
            WHERE 
                s.date BETWEEN :startDate AND :endDate AND sd.date IS NOT NULL
            GROUP BY 
                sua.user_id, u.first_name, u.last_name
        ");
            $statement->execute([
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ]);
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


}
