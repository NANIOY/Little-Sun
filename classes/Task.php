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

    public static function delete($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("DELETE FROM tasks WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
    }

    public function update()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("UPDATE tasks SET title = :title, color = :color WHERE id = :id");
        $statement->bindValue(":id", $this->id);
        $statement->bindValue(":title", $this->title);
        $statement->bindValue(":color", $this->color);
        $statement->execute();
    }

    public static function getById($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTasksByWorkerId($workerId) {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT t.* FROM tasks t JOIN task_user_assignment tua ON t.id = tua.task_id WHERE tua.user_id = :workerId");
        $statement->bindValue(':workerId', $workerId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTaskById($taskId) {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM tasks WHERE id = :taskId");
        $statement->bindValue(":taskId", $taskId);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}