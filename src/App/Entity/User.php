<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Core\Role;

class User implements UserInterface
{
    private $id;

    private $email;

    private $username;

    private $password;

    private $created;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $roles;

    private $confirmationToken;

    const   ROLE_USER   =   'ROLE_USER';

    const   ROLE_ADMIN  =   'ROLE_ADMIN';

    public function __construct()
    {
        $this->roles    =   new ArrayCollection();
        $this->roles->add( static::ROLE_USER );
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername( $username )
    {
        $this->username =   strtolower( (string)$username );
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail( $email )
    {
        $this->email    =   strtolower( (string)$email );
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword( $password )
    {
        $this->password =   (string)$password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $created
     * @return $this
     */
    public function setCreated( \DateTime $created )
    {
        $this->created    =   $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param $confirmationToken
     * @return $this
     */
    public function setConfirmationToken( $confirmationToken )
    {
        $this->confirmationToken    =   (string)$confirmationToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole( $role )
    {
        $role = new Role( strtoupper( $role ) ) ;

        if( !$this->roles->contains( $role ) )
        {
            $this->roles->add( $role );
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole( $role )
    {
        $this->roles->removeElement( new Role( strtoupper( $role ) ) );

        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getUsername();
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return null;
    }
}