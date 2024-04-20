<?php

include_once (__DIR__ . '/../bootstrap.php');

class Admin
{
    private $firstname;
    private $lastname;
    private $email;
    private $password;

    private $role = "admin";
    private $id;
    


    /**
     * Get the value of firstname
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }
    

    /**
     * Get the value of lastname
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }


    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

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

    public function save(){
        $conn = Db::getInstance();
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $statement = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role ) VALUES (:firstname, :lastname, :email, :password, :role)");
        $statement->bindValue(':firstname', $this->getFirstname());
        $statement->bindValue(':lastname', $this->getLastname());
        $statement->bindValue(':email', $this->getEmail());
        $statement->bindValue(':password', $hashedPassword);
        $statement->bindValue(':role', $this->getRole());
        $statement->execute();
        $this->id = $conn->lastInsertId();

    }
}
