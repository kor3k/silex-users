<?php

namespace App\Controller;

use App\Form\SwitchType;
use PEAR2\Net\RouterOS;
use App\RouterOSClient;
use Symfony\Component\HttpFoundation\Request;

class RouterosController extends \Core\AbstractController
{
    protected function fetchUsers()
    {
        $client     =   $this->app['routeros_client'];
        $request    =   new RouterOS\Request( '/ip hotspot user print' );
        $request->setQuery( RouterOS\Query::where( 'profile', 'intr' ) );
        $users      =   $client->fetch( $request );

        unset( $client );

        return $users;
    }

    public function switchAction( Request $request )
    {
        $util       =   $this->app['routeros_util'];
        $form       =   $this->createSwitchForm( $this->fetchUsers() );


        if( 'PUT' === $request->getMethod() )
        {
            $form->submit( $request->request->get( $form->getName() ) );

            if( $form->isValid() )
            {
                $util->changeMenu( '/ip hotspot user' );

                $users      =   $form->get( 'users' )->getData();
                $toDisable  =   array();
                $toEnable   =   array();

                foreach( $users as $key => $user )
                {
                    if( $user['disabled'] )
                    {
                        $toDisable[]  =   $user['.id'];
                        $this->killConnections( $user['name'] );
                    }
                    else
                    {
                        $toEnable[]   =   $user['.id'];
                    }
                }

                $util->enable( implode( ',' , $toEnable ) );
                $util->disable( implode( ',' , $toDisable ) );

                return $this->app->redirect( $this->app->url( 'routeros_get_switch' ) );
            }
        }

        return $this->app->render( '/routeros/switch.html.twig' , [
            'form'   =>  $form->createView() ,
        ] );
    }

    private function killConnections( $username )
    {
        $util   =   $this->app['routeros_util'];
        $util->changeMenu( '/ip hotspot active' );
        $conns  =   $util->find( RouterOS\Query::where( 'user', $username ) );
        $util->remove( $conns );
    }

    protected function createSwitchForm( $users )
    {
        $fb =   $this->app['form.factory']->createBuilder(
                                                    new SwitchType() ,
                                                    [ 'users' => $users ] ,
                                                    [ 'action' => $this->app->url( 'routeros_put_switch' ) ]
                                                        );

        return $fb->getForm();
    }

    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/switch', array( $this , 'switchAction' ) )
            ->bind( 'routeros_get_switch' )
//            ->secure( 'ROLE_INTR' )
        ;

        $controllers->put( '/switch', array( $this , 'switchAction' ) )
            ->bind( 'routeros_put_switch' )
//            ->secure( 'ROLE_INTR' )
        ;

        $this->app['routeros_client']  =
        function()
        {
            $username   =   $this->app['routeros_user'];
            $password   =   $this->app['routeros_password'];
            $ip         =   $this->app['routeros_ip'];

            return new RouterOSClient( $ip , $username , $password );
        };

        $this->app['routeros_util']  =
            function()
            {
                $client =   $this->app['routeros_client'];
                return new RouterOS\Util( $client );
            };

        return $controllers;
    }
}