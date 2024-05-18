<?php
include_once (__DIR__ . '/../bootstrap.php');

class Report
{
    public static function getHoursWorked($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time)) AS hours_worked FROM attendance WHERE user_id = ? AND clock_in_time BETWEEN ? AND ?");
        $statement->execute([$userId, $startDate, $endDate]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTotalHoursWorked($startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time)) AS total_hours_worked FROM attendance WHERE clock_in_time BETWEEN ? AND ?");
        $statement->execute([$startDate, $endDate]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getOvertimeHours($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time) - 8) AS overtime_hours FROM attendance WHERE user_id = ? AND clock_in_time BETWEEN ? AND ? AND TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time) > 8");
        $statement->execute([$userId, $startDate, $endDate]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getSickHours($userId, $startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, clock_in_time, clock_out_time)) AS sick_hours FROM attendance WHERE user_id = ? AND clock_in_time BETWEEN ? AND ? AND EXISTS (SELECT 1 FROM sick_days WHERE user_id = attendance.user_id AND date BETWEEN ? AND ?)");
        $statement->execute([$userId, $startDate, $endDate, $startDate, $endDate]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTimeOffRequests($startDate, $endDate)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM time_off WHERE startDate BETWEEN ? AND ?");
        $statement->execute([$startDate, $endDate]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLatestTimeOffRequests($locationId, $limit = 4) {
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
        $statement = $conn->prepare("SELECT s.*, u.first_name, u.last_name, t.title AS task_title FROM schedules s LEFT JOIN schedule_user_assigned sua ON s.id = sua.schedule_id LEFT JOIN users u ON sua.user_id = u.id LEFT JOIN tasks t ON s.task_id = t.id WHERE s.location_id = ? AND s.date = CURDATE()");
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
}