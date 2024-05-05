<?php
include_once (__DIR__ . '/../bootstrap.php');

class TimeOff
{
    private $id;
    private $startDate;
    private $endDate;
    private $reason;
    


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


    

    public function save()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO time_off (startDate, endDate, reason) VALUES (:startDate, :endDate, :reason)");
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $reason = $this->getReason();
        $statement->bindValue(":startDate", $startDate);
        $statement->bindValue(":endDate", $endDate);
        $statement->bindValue(":reason", $reason);
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
        $statement = $conn->prepare("UPDATE time_off (start_date, end_date, reason) VALUES (:start_date, :end_date, :reason)");
        $start_date = $this->getStartDate();
        $end_date = $this->getEndDate();
        $reason = $this->getReason();
        $statement->bindValue(":start_date", $start_date);
        $statement->bindValue(":end_date", $end_date);
        $statement->bindValue(":reason", $reason);
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


}