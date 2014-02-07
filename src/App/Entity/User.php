<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class User
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

    /**
     * @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    private static $passwordEncoder;

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
     * @param string $rawPassword
     * @return $this
     */
    public function setPassword( $rawPassword )
    {
        $rawPassword       =   (string)$rawPassword;
        $this->password =   static::getPasswordEncoder()->encodePassword( $rawPassword , $rawPassword );

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
     * @param string $rawPassword
     * @return bool
     */
    public function isPasswordValid( $rawPassword )
    {
        return static::getPasswordEncoder()->isPasswordValid( $this->password , $rawPassword , $this->password );
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
        $role = strtoupper( $role ) ;

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
        $this->roles->removeElement( strtoupper( $role ) );

        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    public static function getPasswordEncoder()
    {
        if( !( static::$passwordEncoder instanceof PasswordEncoderInterface ) )
        {
            static::$passwordEncoder    =   new MessageDigestPasswordEncoder( 'ripemd160' , true , 50 );
        }

        return static::$passwordEncoder;
    }
}