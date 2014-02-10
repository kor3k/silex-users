<?php

namespace App\Controller;

class SecuredController extends \Core\AbstractController
{
    public function getIndexAction()
    {
        return $this->app->render( 'secured.html.twig' , [] );
    }

    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/index', array( $this , 'getIndexAction' ) )
            ->bind( 'secured_index' )
            ->secure( 'ROLE_USER' )
        ;

        return $controllers;
    }
}