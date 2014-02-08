<?php

namespace App\Controller;

class FrontendController extends \Core\AbstractController
{
    public function notFoundAction()
    {	
	    return $this->app->render( '/error/404.html.twig' , [] )->setStatusCode( 404 );
    }     
    
    public function errorAction()
    {	
	    return $this->app->render( '/error/error.html.twig' , [] )->setStatusCode( 500 );
    }      
    
    public function indexAction()
    {
	    return $this->app->render( 'index.html.twig' , [] );
    }        
    
    public function partnersAction()
    {	
	    return $this->app->render( 'partners.html.twig' , [ 'partners' => $this->getPartners() ] );
    }    
   
    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/404', array( $this , 'notFoundAction' ) )
                ->bind( '404' )
                    ;

        $controllers->get( '/error', array( $this , 'errorAction' ) )
                ->bind( 'error' )
                    ;

        $controllers->get( '/index', array( $this , 'indexAction' ) )
                ->bind( 'index' )
                    ;

        $controllers->get( '/partners', array( $this , 'partnersAction' ) )
                ->bind( 'partners' )
                    ;
        
        return $controllers;	
    }    
    
    
    /**************************************************************/
    
/**
 * 
 * @return array
 */    
    protected function getPartners()
    {
        return
        [
            [ 'name' => 'Mike Litoris' , 'phone' => '1234' ] ,
            [ 'name' => 'Hugh Jazz' , 'phone' => '5678' ] ,
        ];
    }
}