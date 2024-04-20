<?php
include_once (__DIR__ . '/../bootstrap.php');

class Manager
{
    private $firstName;
    private $lastName;
    private $email;
    private $password;
    private $profileImg;
    private $hubLocation;
    private $role = 'manager';
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
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->password = $hashedPassword;
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

    public static function getById($id)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare('SELECT users.*, locations.name AS location_name FROM users LEFT JOIN locations ON users.location_id = locations.id WHERE users.role = "manager" AND users.id = :id');
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
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

    public function assignToLocation($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("INSERT INTO location_manager (location_id, manager_id) VALUES (:location_id, :manager_id)");
        $statement->bindValue(':location_id', $locationId);
        $statement->bindValue(':manager_id', $this->getId());
        $statement->execute();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare('SELECT users.*, locations.name AS location_name FROM users LEFT JOIN locations ON users.location_id = locations.id WHERE users.role = "manager"');
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $conn = Db::getInstance();
        $this->updateUserDetails($conn);
        $this->updateLocationManager($conn);
    }

    private function updateUserDetails($conn)
    {
        $password = $this->getPassword();
        if (!empty($password)) {
            $statement = $conn->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, password = :password, profile_img = :profile_img, location_id = :location_id WHERE id = :id');
            $statement->bindValue(':password', $password);
        } else {
            $statement = $conn->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, profile_img = :profile_img, location_id = :location_id WHERE id = :id');
        }

        $statement->bindValue(':first_name', $this->getFirstName());
        $statement->bindValue(':last_name', $this->getLastName());
        $statement->bindValue(':email', $this->getEmail());
        $statement->bindValue(':profile_img', $this->getProfileImg());
        $statement->bindValue(':location_id', $this->getHubLocation());
        $statement->bindValue(':id', $this->getId());
        $statement->execute();
    }

    private function updateLocationManager($conn)
    {
        $locationManagerStatement = $conn->prepare('SELECT * FROM location_manager WHERE manager_id = :manager_id');
        $locationManagerStatement->bindValue(':manager_id', $this->getId());
        $locationManagerStatement->execute();
        $locationManagerExists = $locationManagerStatement->fetch(PDO::FETCH_ASSOC);

        if ($locationManagerExists) {
            $updateLocationManagerStatement = $conn->prepare('UPDATE location_manager SET location_id = :location_id WHERE manager_id = :manager_id');
            $updateLocationManagerStatement->bindValue(':location_id', $this->getHubLocation());
            $updateLocationManagerStatement->bindValue(':manager_id', $this->getId());
            $updateLocationManagerStatement->execute();
        } else {
            $insertLocationManagerStatement = $conn->prepare('INSERT INTO location_manager (manager_id, location_id) VALUES (:manager_id, :location_id)');
            $insertLocationManagerStatement->bindValue(':manager_id', $this->getId());
            $insertLocationManagerStatement->bindValue(':location_id', $this->getHubLocation());
            $insertLocationManagerStatement->execute();
        }
    }
}