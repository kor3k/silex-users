<?php

namespace App\Controller;

use App\Form\SwitchType;
use PEAR2\Net\RouterOS;
use Symfony\Component\HttpFoundation\Request;

class RouterosController extends \Core\AbstractController
{
    public function indexAction()
    {
        /**
         * @var $client \PEAR2\Net\RouterOS\Client
         */
        $client = $this->app['routeros_client'];

        var_dump($this->fetchUsers());


        $responses = $client->sendSync( new RouterOS\Request( '/ip hotspot user print' ) );

        foreach( $responses as $key => $response )
        {
            if( $response->getType() !== RouterOS\Response::TYPE_DATA )
            {
                continue;
            }

            var_dump( $response->getAllArguments() );
        }

        foreach ($responses as $response) {
            if ($response->getType() === RouterOS\Response::TYPE_DATA) {
                echo 'IP: ', $response->getArgument('address'),
                ' MAC: ', $response->getArgument('mac-address'),
                "\n";
            }
        }
//

        return 'ok';
    }

    /**
     * @return array
     */
    protected function fetchUsers()
    {
        /**
         * @var $client \PEAR2\Net\RouterOS\Client
         */
        $client     =   $this->app['routeros_client'];
        $responses  =   $client->sendSync( new RouterOS\Request( '/ip hotspot user print' ) );
        $users      =   $this->transformResponses( $responses );

        return $users;
    }

    protected function transformResponses( $responses , $type = RouterOS\Response::TYPE_DATA )
    {
        $ret    =   array();

        foreach( $responses as $key => $response )
        {
            if( $response->getType() !== $type )
            {
                continue;
            }

            $args    =  $response->getAllArguments();
            foreach( $args as $argKey => &$argVal )
            {
                if( 'false' === $argVal )
                {
                    $argVal =   false;
                }
                else if( 'true' === $argVal )
                {
                    $argVal =   true;
                }
            }
            unset($argKey,$argVal);

            $ret[] =   $args;
        }

        return $ret;
    }

    public function switchAction( Request $request )
    {
        /**
         * @var $client \PEAR2\Net\RouterOS\Util
         */
        $util   =   $this->app['routeros_util'];
        $form   =   $this->createSwitchForm( $this->fetchUsers() );


        if( 'PUT' === $request->getMethod() )
        {
            $form->submit( $request->request->get( $form->getName() ) );

            if( $form->isValid() )
            {
                $util->changeMenu( '/ip hotspot user' );

                if( $form->get( 'on' )->isClicked() )
                {
                    $util->enable( RouterOS\Query::where( 'profile', 'intr' ) );
                }
                else if( $form->get( 'off' )->isClicked() )
                {
                    $util->disable( RouterOS\Query::where( 'profile', 'intr' ) );
                }
                else
                {
                    $users  =   $form->get( 'users' )->getData();
                    $toDisable  =   array();
                    $toEnable   =   array();

                    foreach( $users as $key => $user )
                    {
                        if( $user['disabled'] )
                        {
                            $toDisable[]  =   $user['.id'];
                        }
                        else
                        {
                            $toEnable[]   =   $user['.id'];
                        }
                    }

                    $util->enable( implode( ',' , $toEnable ) );
                    $util->disable( implode( ',' , $toDisable ) );
                }

                return $this->app->redirect( $this->app->url( 'routeros_get_switch' ) );
            }
        }

        return $this->app->render( '/routeros/switch.html.twig' , [
            'form'   =>  $form->createView() ,
        ] );
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
        $controllers->get( '/', array( $this , 'indexAction' ) )
            ->bind( 'routeros_index' )
//            ->secure( 'ROLE_INTR' )
        ;

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

            return new RouterOS\Client( $ip , $username , $password );
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