<?php

namespace App\Controller;

use Silex\Route\SecurityTrait;

class SecuredController extends \Core\AbstractController
{
    use SecurityTrait;

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