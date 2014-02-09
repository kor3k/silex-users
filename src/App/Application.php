<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\ChainUserProvider ,
    Symfony\Component\Security\Core\User\InMemoryUserProvider;

class Application extends \Core\Application
{
    public function boot()    
    {		
        //inicializace potřebných služeb
        $this->initMonolog();
        $this->initTwig();
        $this->initUrlGenerator();
        $this->initSwiftGmailer( $this['mailer_user'] , $this['mailer_password'] );
        $this->initValidator();
        $this->initForm();
        $this->initDoctrine( $this['database_host'] , $this['database_name'] , $this['database_user'] , $this['database_password'] );
        $this->initDoctrineOrm( $this['database_name'] );
        $this->initBasicHttpSecurity( '^/admin' , [ 'admin' => [ 'ROLE_ADMIN' , $this['admin_password'] ] ] );
        $this->initHttpCache( $this['cache_ttl'] );

        $this->initNativeSession();
        $this->initSecurity();
        //logování requestů - na ostrém raději vypnout pokud je pomalý filesystem
        $this->before(
        function( Request $request )
        {
            $this->logRequest( $request );
        },
        Application::EARLY_EVENT );

        //nabootování silex aplikace. jen do této chvíle je možné inicializovat služby
        parent::boot();

        //připojení security controlleru
        $securityCtrlr  =   new \App\Controller\SecurityController( $this );
        $this->mount( '/' , $securityCtrlr() );

        $this->after(function (Request $request, Response $response)
        {
            $response
                ->setPrivate()
                ->setClientTtl(0)
                ->setTtl(0);
        });
    }

    protected function initSecurity( array $security = array() )
    {
        //je potřeba, aby byl uživatel přihlášený napříč celou aplikací, ale zároveň část aplikace byla dostupná i bez přihlášení
        //zabezpečené routy/controller akce nastavit příslušnou rolí přes (SecurityTrait)Route::secure nebo přes security.access_rules

        $security   =
        [
            'security.firewalls' =>
            [
                'admin' => array(
                    'pattern' => '^/.*',
                    'anonymous' =>  true ,
                    'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                    'logout' => array( 'logout_path' => '/logout' , 'target_url' => '/login' ),
                    'switch_user' => array('parameter' => '_switch_user', 'role' => 'ROLE_ALLOWED_TO_SWITCH'),
                    'users' => $this->share(function ()
                    {
                        $entityProv    =   new EntityUserProvider( $this['doctrine'] , 'App\Entity\User' );
                        $inMemoryProv  =   new InMemoryUserProvider(
                                            [
                                                'iddqd' =>  [ 'roles' => [ 'ROLE_IDDQD' ] , 'password' => $this['admin_password'] ] ,
                                            ] );

                        return new ChainUserProvider([ $inMemoryProv , $entityProv ]);
                    }),
                ),
            ] ,
            'security.role_hierarchy' =>
            [
                'ROLE_ADMIN'    =>  [ 'ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH' ] ,
                'ROLE_IDDQD'    =>  [ 'ROLE_ADMIN' ] ,
            ] ,
            'security.access_rules' =>
            [
                [ '^/admin', 'ROLE_ADMIN', 'https' ] ,
                [ '^/secured' , 'ROLE_USER' , 'https' ] ,
                [ '^/login' , 'IS_AUTHENTICATED_ANONYMOUSLY' , 'https' ] ,
                [ '^/login_check' , 'IS_AUTHENTICATED_ANONYMOUSLY' , 'https' ] ,
                [ '^/.*' , 'IS_AUTHENTICATED_ANONYMOUSLY' ] ,
            ] ,
        ];

        parent::initSecurity( $security );
    }
}