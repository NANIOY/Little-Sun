<?php
include_once(__DIR__ . '/../bootstrap.php');

class Location
{
    private $id;
    private $name;
    private $address;
    private $contactInfo;
    private $managerId;

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

    /**
     * Get the value of managerId
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * Set the value of managerId
     *
     * @return  self
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;

        return $this;
    }

    public function save()
    {
        $conn = Db::getInstance();

        $statement = $conn->prepare('insert into locations (name, address, contact_info, manager_id) values (:name, :address, :contact_info, :manager_id)');

        $statement->bindValue(':name', $this->name);
        $statement->bindValue(':address', $this->address);
        $statement->bindValue(':contact_info', $this->contactInfo);
        $statement->bindValue(':manager_id', $this->managerId);

        $statement->execute();
    }

    public static function getAll()
    {
        $conn = Db::getInstance();

        $statement = $conn->query('select * from locations');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
