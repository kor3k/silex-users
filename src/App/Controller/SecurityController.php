<?php

namespace App\Controller;

use Silex\Route\SecurityTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\SecurityEvents ,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends \Core\AbstractController
{
    use SecurityTrait;

    public function loginAction( Request $request )
    {
        return $this->app->render( 'login.html.twig' ,
                                    [
                                       'last_username' =>   $this->app['session']->get( '_security.last_username' ) ,
                                       'last_error'    =>   $this->app['security.last_error']( $request ) ,
                                    ] );
    }

    protected function connect( \Silex\ControllerCollection $controllers )
    {

        $this->app->on( SecurityEvents::INTERACTIVE_LOGIN ,
            function( InteractiveLoginEvent $event )
            {
                $this->updateLastLogin( $event );
            });

        $controllers->get( '/login', array( $this , 'loginAction' ) )
                    ->bind( 'login' )
        ;

        return $controllers;
    }


    protected function updateLastLogin( InteractiveLoginEvent $event , $oncePerDay = false )
    {
        $user   =   $event->getAuthenticationToken()->getUser();
        $now    =   new \DateTime();
        $last   =   $user->getLastLogin();

        if( $oncePerDay && $last && $now->diff( $last , true )->days < 1 )
        {
            return;
        }

        $em =   $this->app['orm.em'];
        $user->setLastLogin( $now );
        $em->persist( $user );
        $em->flush();
    }
}