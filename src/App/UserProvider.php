<?php

namespace App;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User as SecurityUser;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserProvider extends EntityUserProvider
{
    protected $repository;

    protected $embeddedUsers    =   array();

    public function __construct(ManagerRegistry $registry, $class, $property = null, $managerName = null , array $embeddedUsers = array() )
    {
        $this->repository       =   $registry->getManager( $managerName )->getRepository( $class );
        $this->embeddedUsers    =   $embeddedUsers;

        parent::__construct( $registry , $class , $property , $managerName );
    }

    /**
     * @return \Symfony\Component\Security\Core\User\User
     */
    private function getEmbeddedUser( $username )
    {
        return new SecurityUser(
            $username ,
            $this->embeddedUsers[$username][1] ,
            $this->embeddedUsers[$username][0] ,
            true, true, true, true);
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface[]
     */
    public function getEmbeddedUsers()
    {
        $ret    =   array();

        foreach( $this->embeddedUsers as $username  => $val )
        {
            $ret[$username] =   $this->getEmbeddedUser( $username );
        }

        return $ret;
    }

    /**
     * @param string $usernameOrEmail
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername( $usernameOrEmail )
    {
        if( !empty( $this->embeddedUsers[$usernameOrEmail] ) )
        {
            return $this->getEmbeddedUser( $usernameOrEmail );
        }

        $user   =   $this->repository->findOneBy([ 'username' => strtolower( $usernameOrEmail ) ]);

        if( !$user )
        {
            $user   =   $this->repository->findOneBy([ 'email' => strtolower( $usernameOrEmail ) ]);
        }

        if( !$user )
        {
            throw new UsernameNotFoundException( sprintf( 'User "%s" not found.', $usernameOrEmail ) );
        }

        return $user;
    }
}