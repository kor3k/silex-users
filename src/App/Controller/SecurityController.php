<?php

namespace App\Controller;

use Silex\Route\SecurityTrait;
use Symfony\Component\HttpFoundation\Request;

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

//        $this->app->on( \Symfony\Component\Security\Http\SecurityEvents::INTERACTIVE_LOGIN ,
//            function( \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event )
//            {
//                $this->updateLastLogin( $event );
//            });

        $controllers->get( '/login', array( $this , 'loginAction' ) )
                    ->bind( 'login' )
        ;

        return $controllers;
    }


    protected function updateLastLogin( \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event , $oncePerDay = false )
    {
        $user   =   $event->getAuthenticationToken()->getUser();
        $now    =   new \DateTime();
        $last   =   $user->getLastLogin();

        if( $oncePerDay && $now->diff( $last , true )->days < 1 )
        {
            return;
        }

        $em =   $this->app['orm.em'];
        $user->setLastLogin( $now );
        $em->update( $user );
        $em->flush();
    }
}