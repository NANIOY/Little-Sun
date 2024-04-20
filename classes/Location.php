<?php
include_once (__DIR__ . '/../bootstrap.php');

class Location
{
    private $id;
    private $name;
    private $address;
    private $contactInfo;
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

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of contactInfo
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }

    /**
     * Set the value of contactInfo
     *
     * @return  self
     */
    public function setContactInfo($contactInfo)
    {
        $this->contactInfo = $contactInfo;

        return $this;
    }

    public function save()
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('insert into locations (name, address, contact_info) values (:name, :address, :contact_info)');

        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':address', $this->address);
        $statement->bindValue(':contact_info', $this->contactInfo);

        $statement->execute();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();

        $statement = $conn->query('select * from locations');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($id)
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('delete from locations where id = :id');
        $statement->bindValue(':id', $id);
        $statement->execute();
    }

    public static function getManagersByLocationId($locationId)
    {
        $conn = Db::getInstance();
        $statement = $conn->prepare('SELECT users.first_name, users.last_name FROM users JOIN location_manager ON users.id = location_manager.manager_id WHERE location_manager.location_id = :location_id');
        $statement->bindValue(':location_id', $locationId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
