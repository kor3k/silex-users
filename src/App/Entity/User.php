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

    private $lastLogin;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $roles;

    private $confirmationToken;

    const   ROLE_USER   =   'ROLE_USER';
    const   ROLE_ADMIN  =   'ROLE_ADMIN';
    const   ROLE_IDDQD  =   'ROLE_IDDQD';

    public function __construct()
    {
        $this->setUserRoles( [ static::ROLE_USER ] );
    }

    /**
     * @return array
     */
    public static function getAvailableRoles()
    {
        $ref    =   new \ReflectionClass( get_called_class() );
        $roles  =   array();

        foreach( $ref->getConstants() as $name => $val )
        {
            if( 'ROLE_' !== substr( $name , 0 , 5 ) )
            {
                continue;
            }

            $roles[$val]    =   $val;
        }

        return $roles;
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
     * @param $lastLogin
     * @return $this
     */
    public function setLastLogin( \DateTime $lastLogin )
    {
        $this->lastLogin    =   $lastLogin;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
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
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles->map(
            function( $role )
            {
                return new Role( $role );
            })->toArray();
    }

    /**
     * @return array
     */
    public function getUserRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setUserRoles( array $roles )
    {
        $this->roles    =   new ArrayCollection();
        foreach( $roles as $key => $role )
        {
            $this->roles->add( strtoupper( (string)$role ) );
        }

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