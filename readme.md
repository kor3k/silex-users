#Silex Users App

-----------

## Popis
- registruje a spravuje uživatele v databázi. 
- registrovaní uživatelé se mohou přihlašovat do aplikace. 
- některé routy / controllery vyžadují přihlášeného uživatele, jiné ne.
- je možné i nadefinovat uživatele v configu
- pro model data používá ORM + MySQL
- pro views Twig
- pro zabezpečení Symfony\Security component
- pro uchování uživatelského kontextu Symfony\Session
- Symfony\Translation pro překlady prvků aplikace, zvolený jazyk ukládá do Session
    - což porušuje bezestavovost http protokolu a REST principy (jedna url vrací různá data na základě kontextu). správná implementace by měla brát v potaz `Accept-Language` request header a/nebo `_locale` query parameter
- [silex security provider](http://silex.sensiolabs.org/doc/providers/security.html)
- pro html/css layout [unsemantic](http://unsemantic.com/), pro html/css ui [bootstrap](http://getbootstrap.com/)

-----------

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

## Použití

Po přihlášení uživatele je ten dostupný přes `Application::user`, tedy `$app->user()` v php a `{{ app.user }}` v Twigu. 
Pozor ovšem, hodnota metody může být: 
	
- **null** (na routě mimo firewall)
- **string 'anon.'** (na routě za firewallem autorizované anonymně) 
- **Symfony\Component\Security\Core\User\UserInterface** (na routě za fw autorizované plně)

Takže je lepší před přístupem kontrolovat roli `IS_AUTHENTICATED_FULLY` nebo  `ROLE_USER` pomocí `Application::isGranted`:

        if( $app->isGranted( 'ROLE_USER' ) )
        {
     	  echo $app->user()->getUsername();
        }  
        else
        {
     	  echo 'anonymous';
        }

a v Twigu:

       {% if is_granted( 'ROLE_USER' ) %}
           {{ app.user.username }}    
       {% else %} 
           anonymous
       {% endif %}   

- metoda `Application::isGranted` vrací **true** | **false** pokud uživatel má nebo nemá danou roli.
- pokud ji zavoláme s druhým argumentem **true**, tak v případě, že uživatel roli nemá, nevrací false, nýbrž vyhodí `AccessDeniedException`: 

        $app->isGranted( 'ROLE_USER' , true );

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