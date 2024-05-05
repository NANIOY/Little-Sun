<?php
include_once (__DIR__ . '/../bootstrap.php');

class Task
{
    private $id;
    private $start_date;
    private $end_date;
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

  
    public function getStart_date()
    {
        return $this->start_date;
    }

    
    public function setStart_date($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEnd_date()
    {
        return $this->end_date;
    }

  
    public function setEnd_date($end_date)
    {
        $this->end_date = $end_date;

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
        $statement = $conn->prepare("INSERT INTO time_off (start_date, end_date, reason) VALUES (:start_date, :end_date, :reason)");
        $start_date = $this->getStart_date();
        $end_date = $this->getEnd_date();
        $reason = $this->getReason();
        $statement->bindValue(":start_date", $start_date);
        $statement->bindValue(":end_date", $end_date);
        $statement->bindValue(":reason", $reason);
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
        $statement = $conn->prepare("UPDATE time_off (start_date, end_date, reason) VALUES (:start_date, :end_date, :reason)");
        $start_date = $this->getStart_date();
        $end_date = $this->getEnd_date();
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