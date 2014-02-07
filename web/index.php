<?php

define( 'DIR' , __DIR__.'/..' );
define( 'WEB' ,'/users/web' );

require_once DIR.'/vendor/autoload.php';
require_once DIR.'/vendor/kor3k/silex/src/Core/startup.php';

/***********************************/

$config   =	array(
    
    'mailer_user'	    =>  'someuser@gmail.com',
    'mailer_password'	=>  'pa$$word' ,
    'mailer_recipient'	=>  'otheruser@gmail.com' ,
    
    'database_host'	    =>  'localhost',
    'database_name'	    =>  'users',
    'database_user'	    =>  'root',
    'database_password'	=>  '',  
    
    'admin_password'	=>  'vstup' ,
    
    'cache_ttl'		    =>  3600 ,
    'response_ttl'	    =>  3600 ,
    'locale'		    =>  'cs' ,
    'debug'		        =>  true ,
        
);

$app		=   new \App\Application( $config );
$app->boot();


$userController =   new \App\UserController( $app );
$app->mount( '/user' , $userController() );

$frontend	=   new \App\FrontendController( $app );
$app->mount( '/' , $frontend() );

$backend	=   new \App\BackendController( $app );
$app->mount( '/admin' , $backend() );

$app->run();
//$app['http_cache']->run();

//you need to create "logs" and "cache" dir in the silex folder