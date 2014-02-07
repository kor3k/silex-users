<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application extends \Core\Application
{
    public function boot()    
    {		
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

        $this->before(
        function( Request $request )
        {
            $this->logRequest( $request );
        },
        Application::EARLY_EVENT );

        //this must be called at last because it boots registered providers. you must change provider settings until you call this.
        parent::boot();
    }    
}
