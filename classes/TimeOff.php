<?php
include_once (__DIR__ . '/../bootstrap.php');

class TimeOff
{
    private $id;
    private $startDate;
    private $endDate;
    private $reason;
    private $approved;
    private $declineReason;
    // added 
    private $userId;
    private $status;
    
    // added - end



    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }


    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    public function getApproved()
    {
        return $this->approved;
    }

    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    public function getDeclineReason()
    {
        return $this->declineReason;
    }

    public function setDeclineReason($declineReason)
    {
        $this->declineReason = $declineReason;
        return $this;
    }

    // added
    public function getUserId()
    {
        return $this->userId;
    }
    
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    // added - end


    public function save()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO time_off (startDate, endDate, reason, approved, decline_reason, user_id) VALUES (:startDate, :endDate, :reason, :approved, :decline_reason, :user_id)");
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $reason = $this->getReason();
        $approved = $this->getApproved() ?? 0;
        $declineReason = $this->getDeclineReason();
        $user_id = $_SESSION['user']['id'];

        $statement->bindValue(":startDate", $startDate);
        $statement->bindValue(":endDate", $endDate);
        $statement->bindValue(":reason", $reason);
        $statement->bindValue(":approved", $approved, PDO::PARAM_INT);
        $statement->bindValue(":decline_reason", $declineReason);
        $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM tasks");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("UPDATE time_off SET start_date = :start_date, end_date = :end_date, reason = :reason, approved = :approved, decline_reason = :decline_reason WHERE id = :id");
        $start_date = $this->getStartDate();
        $end_date = $this->getEndDate();
        $reason = $this->getReason();
        $approved = $this->getApproved();
        $declineReason = $this->getDeclineReason();

        $statement->bindValue(":start_date", $start_date);
        $statement->bindValue(":end_date", $end_date);
        $statement->bindValue(":reason", $reason);
        $statement->bindValue(":approved", $approved, PDO::PARAM_INT);
        $statement->bindValue(":decline_reason", $declineReason);
        $statement->bindValue(":id", $this->getId(), PDO::PARAM_INT);
        $statement->execute();
    }

    public static function getById($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM time_off WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllForUser($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM time_off WHERE user_id = :user_id ORDER BY startDate DESC");
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllForLocation($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT time_off.*, users.first_name, users.last_name, time_off.approved AS approved FROM time_off JOIN users ON time_off.user_id = users.id WHERE users.location_id = :location_id ORDER BY time_off.startDate DESC");
        $statement->bindValue(':location_id', $locationId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    
}