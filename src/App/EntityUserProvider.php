<?php

namespace App;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider as BaseEntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\Common\Persistence\ManagerRegistry;

class EntityUserProvider extends BaseEntityUserProvider
{
    protected $repository;

    public function __construct(ManagerRegistry $registry, $class, $property = null, $managerName = null )
    {
        $this->repository       =   $registry->getManager( $managerName )->getRepository( $class );
        parent::__construct( $registry , $class , $property , $managerName );
    }

    /**
     * @param string $usernameOrEmail
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername( $usernameOrEmail )
    {
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