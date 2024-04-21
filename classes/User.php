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

    public static function getAllWorkers()
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare("SELECT * FROM users WHERE role = 'worker'");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}