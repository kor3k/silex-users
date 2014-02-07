#Silex

-----------

## Instalace

1. download silex

  - dle instrukcí na [silex webu](http://silex.sensiolabs.org/download)
  - nebo z [kor3k/silex-app](https://bitbucket.org/kor3k/silex-app)

            git clone https://bitbucket.org/kor3k/silex-app.git

2. download composer

        php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

3. [update](#update)

4. vytvořit složky `/cache` a `/logs`

5. [http://localhost/silex-app/web/index](http://localhost/silex-app/web/index)

-----------

<a name="update"></a>
## Update
1. update dependencies

        php composer.phar self-update && php composer.phar update --optimize-autoloader

-----------

## Hints

- cesta k php musí být v systémové Path proměnné:
   - Control Panels -> System -> Advanced system settings -> Environment Variables -> System Variables -> Path -> Edit,
   - přidat k současné hodnotě `;c:\wamp\bin\php\php5.5\`

- příkazy se spouští v git bashi
   - [sourcetree](http://www.sourcetreeapp.com/download/) , Download embedded git
   - [msysgit](http://code.google.com/p/msysgit/downloads/list)

-----------

## Links

 - [github repo](http://github.com/fabpot/Silex)
 - [silex](http://silex.sensiolabs.org/)
 - [twig](http://twig.sensiolabs.org/)
 - [doctrine](http://www.doctrine-project.org/)
 - [symfony form](http://symfony.com/doc/current/reference/forms/types.html)
 - [symfony twig form](http://symfony.com/doc/current/reference/forms/twig_reference.html)
 - [symfony validation](http://symfony.com/doc/current/reference/constraints.html)

-----------


