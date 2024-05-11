<?php
include_once (__DIR__ . '/../bootstrap.php');

class Attendance
{
    public static function clockIn($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO attendance (user_id, clock_in_time) VALUES (:user_id, NOW())");
        $statement->bindValue(':user_id', $userId);
        $statement->execute();
    }

    public static function clockOut($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT clock_in_time FROM attendance WHERE user_id = :user_id AND clock_out_time IS NULL ORDER BY clock_in_time DESC LIMIT 1");
        $statement->bindValue(':user_id', $userId);
        $statement->execute();
        $clockInTime = $statement->fetchColumn();

        if ($clockInTime) {
            $updateStatement = $conn->prepare("UPDATE attendance SET clock_out_time = NOW() WHERE user_id = :user_id AND clock_out_time IS NULL");
            $updateStatement->bindValue(':user_id', $userId);
            $updateStatement->execute();
        }
    }

    public static function getCurrentStatus($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM attendance WHERE user_id = :user_id ORDER BY clock_in_time DESC LIMIT 1");
        $statement->bindValue(':user_id', $userId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $clockedIn = isset($result['clock_out_time']) ? false : true;
            $lastActionTime = $clockedIn ? $result['clock_in_time'] : $result['clock_out_time'];
            return [
                'clocked_in' => $clockedIn,
                'message' => $clockedIn ? "Clocked in at: " . $lastActionTime : "Clocked out at: " . $lastActionTime
            ];
        } else {
            return [
                'clocked_in' => false,
                'message' => "Not clocked in"
            ];
        }
    }

    public static function calculateHoursWorked($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT clock_in_time, clock_out_time FROM attendance WHERE user_id = :user_id AND clock_out_time IS NOT NULL ORDER BY clock_in_time DESC LIMIT 1");
        $statement->bindValue(':user_id', $userId);
        $statement->execute();
        $result = $statement->fetch();

        if ($result) {
            $clockIn = new DateTime($result['clock_in_time']);
            $clockOut = new DateTime($result['clock_out_time']);
            $interval = $clockIn->diff($clockOut);
            return $interval->format('%h hours %i minutes');
        }
        return '0 hours 0 minutes';
    }
}
