<?php
include_once (__DIR__ . '/../bootstrap.php');

class Task
{
    private $id;
    private $title;
    private $color;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    public function save()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO tasks (title, color) VALUES (:title, :color)");
        $title = $this->getTitle();
        $color = $this->getColor();
        $statement->bindValue(":title", $title);
        $statement->bindValue(":color", $color);
        $statement->execute();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM tasks");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}