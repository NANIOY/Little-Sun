<?php
include_once (__DIR__ . '/../bootstrap.php');

class User
{
    private $firstName;
    private $lastName;
    private $email;
    private $password;
    private $profileImg;
    private $hubLocation;
    private $role;
    private $id;


    /**
     * Get the value of firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of profileImg
     */
    public function getProfileImg()
    {
        return $this->profileImg;
    }

    /**
     * Set the value of profileImg
     *
     * @return  self
     */
    public function setProfileImg($profileImg)
    {
        $this->profileImg = $profileImg;

        return $this;
    }

    /**
     * Get the value of hubLocation
     */
    public function getHubLocation()
    {
        return $this->hubLocation;
    }

    /**
     * Set the value of hubLocation
     *
     * @return  self
     */
    public function setHubLocation($hubLocation)
    {
        $this->hubLocation = $hubLocation;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function save()
    {
        $conn = Db::getInstance();
        $hashedPassword = password_hash($this->getPassword(), PASSWORD_DEFAULT);
        $userStatement = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, profile_img, location_id, role) VALUES (:first_name, :last_name, :email, :password, :profile_img, :location_id, :role)");
        $userStatement->bindValue(':first_name', $this->getFirstName());
        $userStatement->bindValue(':last_name', $this->getLastName());
        $userStatement->bindValue(':email', $this->getEmail());
        $userStatement->bindValue(':password', $hashedPassword);
        $userStatement->bindValue(':profile_img', $this->getProfileImg());
        $userStatement->bindValue(':location_id', $this->getHubLocation());
        $userStatement->bindValue(':role', $this->getRole());
        $userStatement->execute();
        $this->id = $conn->lastInsertId();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByEmail($email, $enteredPassword)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare('SELECT * FROM users WHERE email = :email');
        $statement->bindValue(':email', $email);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($enteredPassword, $user['password'])) {
            return $user; // Password is correct
        } else {
            return false; // Password is incorrect or user doesn't exist
        }
    }

    public static function getById($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare('SELECT * FROM users WHERE id = :id');
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllWorkers($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE role = 'worker' AND location_id = :location_id");
        $statement->bindValue(':location_id', $locationId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $conn = Db::getInstance();

        if ($this->getHubLocation() === null) {
            $statement = $conn->prepare("SELECT location_id FROM users WHERE id = :id");
            $statement->bindValue(':id', $this->getId());
            $statement->execute();
            $currentLocation = $statement->fetch(PDO::FETCH_ASSOC);
            $location_id = $currentLocation['location_id'];
        } else {
            $location_id = $this->getHubLocation();
        }

        $userStatement = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, profile_img = :profile_img, location_id = :location_id WHERE id = :id");
        $userStatement->bindValue(':first_name', $this->getFirstName());
        $userStatement->bindValue(':last_name', $this->getLastName());
        $userStatement->bindValue(':email', $this->getEmail());
        $userStatement->bindValue(':profile_img', $this->getProfileImg());
        $userStatement->bindValue(':location_id', $location_id);
        $userStatement->bindValue(':id', $this->getId());
        $userStatement->execute();
    }

    public function assignTasks($tasks)
    {
        $conn = Db::getInstance();
        $conn->beginTransaction();

        try {
            $statement = $conn->prepare("DELETE FROM task_user_assignment WHERE user_id = :user_id");
            $statement->bindValue(':user_id', $this->id, PDO::PARAM_INT);
            $statement->execute();

            $statement = $conn->prepare("INSERT INTO task_user_assignment (user_id, task_id) VALUES (:user_id, :task_id)");
            foreach ($tasks as $task_id) {
                $statement->bindValue(':user_id', $this->id, PDO::PARAM_INT);
                $statement->bindValue(':task_id', $task_id, PDO::PARAM_INT);
                $statement->execute();
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    public function getAssignedTasks()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM task_user_assignment WHERE user_id = :user_id");
        $statement->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAssignedSchedule($userId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM schedule_user_assigned WHERE user_id = :user_id");
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchSchedule($userId, $monthYear)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("
            SELECT schedules.*, tasks.title as task_title, tasks.color, users.first_name, users.last_name
            FROM schedules
            INNER JOIN schedule_user_assigned ON schedules.id = schedule_user_assigned.schedule_id
            INNER JOIN tasks ON schedules.task_id = tasks.id
            INNER JOIN users ON schedule_user_assigned.user_id = users.id
            WHERE schedules.date LIKE :monthYear AND users.id = :userId
        ");
        $statement->bindValue(':monthYear', $monthYear . '%');
        $statement->bindValue(':userId', $userId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function assignSick($userId, $date, $reason)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO sick_days (user_id, date, reason) VALUES (:user_id, :date, :reason)");
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':date', $date);
        $statement->bindValue(':reason', $reason);
        $statement->execute();
    }

    public static function getSickDays($userId, $year, $month)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT date FROM sick_days WHERE user_id = :user_id AND YEAR(date) = :year AND MONTH(date) = :month");
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':year', $year);
        $statement->bindValue(':month', $month);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function checkExistingSickDay($userId, $date)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM sick_days WHERE user_id = :user_id AND date = :date");
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':date', $date);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}