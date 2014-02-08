<?php

namespace App;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User as SecurityUser;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserProvider extends EntityUserProvider
{
    protected $class;
    protected $repository;
    protected $property;
    protected $metadata;

    protected $embeddedUsers    =   array();

    public function __construct(ManagerRegistry $registry, $class, $property = null, $managerName = null , array $embeddedUsers = array() )
    {
        $this->embeddedUsers    =   $embeddedUsers;

        $em = $registry->getManager($managerName);
        $this->class = $class;
        $this->metadata = $em->getClassMetadata($class);

        if (false !== strpos($this->class, ':')) {
            $this->class = $this->metadata->getName();
        }

        $this->repository = $em->getRepository($class);
        $this->property = $property;
    }

    public function loadUserByUsername( $usernameOrEmail )
    {
        if( !empty( $this->embeddedUsers[$usernameOrEmail] ) )
        {
            return new SecurityUser(
                $usernameOrEmail ,
                $this->embeddedUsers[$usernameOrEmail][1] ,
                $this->embeddedUsers[$usernameOrEmail][0] ,
                true, true, true, true);
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

        return new SecurityUser(
                                    $user->getUsername() ,
                                    $user->getPassword() ,
                                    $user->getRoles() ,
                                    true, true, true, true);
    }
}