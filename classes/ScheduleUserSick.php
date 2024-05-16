<?php

include_once (__DIR__ . '/../bootstrap.php');

class ScheduleUser {

    private $conn;
    
    public function assignSickDay($userId, $date, $startTime, $endTime, $reason) {
        $sql = "INSERT INTO sick_days (user_id, date, start_time, end_time, reason) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Error preparing the statement: " . $this->conn->error);
        }

        $stmt->bind_param("issss", $userId, $date, $startTime, $endTime, $reason);
        $stmt->execute();
        $stmt->close();
    }
}
?>
