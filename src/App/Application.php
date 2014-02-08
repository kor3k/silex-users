<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    }

    protected function initSecurity( array $security = array() )
    {
        //je potřeba, aby byl uživatel přihlášený napříč celou aplikací, ale zároveň část aplikace byla dostupná i bez přihlášení
        //zabezpečené controller actions nastavit příslušnou rolí přes (SecurityTrait)Controller::secure

        $security   =
        [
            'security.firewalls' =>
            [
                'admin' => array(
                    'pattern' => '^/.*',
                    'anonymous' =>  true ,
                    'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                    'logout' => array( 'logout_path' => '/logout' , 'target_url' => '/login' ),
                    'users' => $this->share(function ()
                    {
                        return new UserProvider( $this['doctrine'] , 'App\Entity\User' , null , null ,
                            [
                                'admin' =>  [ 'ROLE_IDDQD' , $this['admin_password'] ] ,
                            ] );
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
                [ '^.*$' , 'IS_AUTHENTICATED_ANONYMOUSLY' ] ,
                [ '^/login' , 'IS_AUTHENTICATED_ANONYMOUSLY' , 'https' ] ,
                [ '^/login_check' , 'IS_AUTHENTICATED_ANONYMOUSLY' , 'https' ] ,
            ] ,
        ];

        parent::initSecurity( $security );
    }
}
