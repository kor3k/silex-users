#Silex Users App

-----------

## Popis
- registruje a spravuje uživatele v databázi. 
- registrovaní uživatelé se mohou přihlašovat do aplikace. 
- některé routy / controllery vyžadují přihlášeného uživatele, jiné ne.
- je možné i nadefinovat uživatele v configu
- pro model data používá ORM + MySQL
- pro views používá Twig
- pro zabezpečení používá Symfony\Security component
- pro uchování uživatelského kontextu používá Session

## Instalace

1. download silex-users

  - z githubu [kor3k/silex-users](https://github.com/kor3k/silex-users)

            git clone https://github.com/kor3k/silex-users.git

2. download composer

        php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

3. [update](#update)

4. vytvořit složky `/cache` a `/logs`

5. vytvořit schéma v databázi
   -  dle `/Resources/sql/users.sql`
   -  v MySQL Workbench - Open `/Resources/sql/users.mwb` , Database -> Synchronize Model

6. upravit nastavení ve front controlleru `/web/index.php` - připojení k db, mailer user atd

7. [http://localhost/silex-users/web/index.php/index](http://localhost/silex-users/web/index.php/index)


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

 - [silex](http://silex.sensiolabs.org/)
 - [twig](http://twig.sensiolabs.org/)
 - [doctrine](http://www.doctrine-project.org/)
 - [symfony form](http://symfony.com/doc/current/reference/forms/types.html)
 - [symfony twig form](http://symfony.com/doc/current/reference/forms/twig_reference.html)
 - [symfony validation](http://symfony.com/doc/current/reference/constraints.html)
 - [silex repo](http://github.com/fabpot/Silex)

-----------





