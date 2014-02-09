<?php

// prvně je třeba vytvořit adresáře "cache" a "logs"
// v /logs/dev.log a /logs/prod.log jsou pak logy aplikace, eventuálně i requestů
// front controller - na něj musí směrovat všechny requesty - viz .htaccess

// nastavení konstant s adresářem a base url
define( 'DIR' , __DIR__.'/..' );
define( 'WEB' ,'/silex-users/web' );

// načtení autoloaderu
require_once DIR.'/vendor/autoload.php';
require_once DIR.'/vendor/kor3k/silex/src/Core/startup.php';

/***********************************/


// nastavení aplikace
// na ostrém vypnout debug

$config   =	array(
    
    'mailer_user'	    =>  'someuser@gmail.com',
    'mailer_password'	=>  'pa$$word' ,
    'mailer_recipient'	=>  'otheruser@gmail.com' ,
    
    'database_host'	    =>  'localhost',
    'database_name'	    =>  'users',
    'database_user'	    =>  'root',
    'database_password'	=>  '',  
    
    'admin_password'	=>  'idkfa' ,
    
    'cache_ttl'		    =>  3600 ,
    'response_ttl'	    =>  3600 ,
    'locale'		    =>  'cs' ,
    'debug'		        =>  true ,
        
);

//instancování aplikace/silexu, nabootování
$app		=   new \App\Application( $config );
$app->boot();

//instancování controllerů a napojení na app
//to se může přesunout do App\Application::boot, ale musí se prvně zavolat Silex\Application::boot
//anebo do Controller::connect ... matter of taste
$user =   new \App\Controller\UserController( $app );
$app->mount( '/users' , $user() );

$secured =   new \App\Controller\SecuredController( $app );
$app->mount( '/secured' , $secured() );

$frontend	=   new \App\Controller\FrontendController( $app );
$app->mount( '/' , $frontend() );

$backend	=   new \App\Controller\BackendController( $app );
$app->mount( '/admin' , $backend() );

//spuštění aplikace
//na ostrém spustíme z cache
$app->run();
//$app['http_cache']->run();
