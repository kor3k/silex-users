<?php

namespace App\Controller;

class BackendController extends \Core\AbstractController
{
    public function indexAction()
    {	
	    return $this->app->render( '/admin/index.html.twig' , [] );
    }
  
    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/', array( $this , 'indexAction' ) )
                ->bind( 'admin_index' )
                    ;
                        
        
        return $controllers;	
    }        
}
