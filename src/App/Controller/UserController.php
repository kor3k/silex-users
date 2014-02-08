<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class UserController extends \Core\AbstractController
{
    public function getRegisterAction()
    {
        return $this->app->render( '/user/registration.html.twig' , [ 'form' => $this->createRegistrationForm()->createView() ] );
    }

    public function postRegisterAction( Request $request )
    {
        $form   =   $this->createRegistrationForm();
        $form->submit( $request->request->get( 'registration' ) );

        if( $form->isValid() )
        {
            $user   =   $form->getData();
            $em     =   $this->app['orm.em'];

            $user->setPassword( $this->app->encodePassword( $user , $user->getPassword() ) );
            $em->persist( $user );
            $em->flush();

            return $this->app->render( '/user/registrationComplete.html.twig' , [ 'user' => $user ] );
        }
        else
        {
            return $this->app->render( '/user/registration.html.twig' , [ 'form' => $form->createView() ] );
        }
    }

    public function cgetAction()
    {
        $users  =   $this->getUserRepository()->findAll();

        return $this->app->render( '/user/list.html.twig' , [ 'users' => $users ] );
    }


    public function editAction( Request $request , $user )
    {
        $user   =   $this->getUserRepository()->findOneBy([ 'id' => $user ]);

        if( !$user )
        {
            $this->app->abort( 404 , 'uživatel nenalezen' );
        }

        $form   =   $this->createEditForm( $user );

        if( 'PUT' === $request->getMethod() )
        {
            $form->submit( $request->request->get( 'user' ) );

            if( $form->isValid() )
            {
                $user   =   $form->getData();
                $em     =   $this->app['doctrine']->getManager();

                $em->persist( $user );
                $em->flush();

                return $this->app->redirect( $this->app->url( 'edit_user' , [ 'user' => $user->getId() ] ) );
            }
        }

        return $this->app->render( '/user/edit.html.twig' , [
            'user'  =>  $user ,
            'form'  =>  $form->createView() ,
        ] );
    }

    public function deleteAction( $user )
    {
        $user   =   $this->getUserRepository()->findOneBy([ 'id' => $user ]);

        if( !$user )
        {
            $this->app->abort( 404 , 'uživatel nenalezen' );
        }

        $em     =   $this->app['orm.em'];
        $em->remove( $user );
        $em->flush();

        return $this->app->redirect( $this->app->url( 'get_users' ) );
    }

    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/register', array( $this , 'getRegisterAction' ) )
            ->bind( 'get_register_user' )
        ;

        $controllers->post( '/register', array( $this , 'postRegisterAction' ) )
            ->bind( 'post_register_user' )
        ;

        $controllers->get( '/', array( $this , 'cgetAction' ) )
            ->bind( 'get_users' )
            ->secure( 'ROLE_ADMIN' )
        ;

        $controllers->get( '/{user}', array( $this , 'getAction' ) )
            ->bind( 'get_user' )
            ->secure( 'ROLE_ADMIN' )
        ;

        $controllers->put( '/{user}', array( $this , 'editAction' ) )
            ->bind( 'put_user' )
            ->secure( 'ROLE_ADMIN' )
        ;

        $controllers->delete( '/{user}', array( $this , 'deleteAction' ) )
            ->bind( 'delete_user' )
            ->secure( 'ROLE_ADMIN' )
        ;

        $controllers->get( '/{user}/edit', array( $this , 'editAction' ) )
            ->bind( 'edit_user' )
            ->secure( 'ROLE_ADMIN' )
        ;

        return $controllers;
    }


    /********************/

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getUserRepository()
    {
        return $this->app['orm.em']->getRepository( 'App\Entity\User' );
    }

    protected function createEditForm( User $user )
    {
        $roles  =   [ 'ROLE_USER' => 'ROLE_USER' , 'ROLE_ADMIN' => 'ROLE_ADMIN' ];
        $fb =   $this->app['form.factory']->createNamedBuilder( 'user' , 'form' , $user );
        $fb
            ->add( 'username' , 'text' , [ 'disabled' => true ] )
            ->add( 'email' , 'email' , [ 'disabled' => true ] )
            ->add( 'userRoles' , 'choice' ,
                    [
                        'multiple'      => true ,
                        'constraints'   =>  [ new Constraints\Choice([ 'choices' => array_values( $roles ) , 'multiple' => true ]) ] ,
                        'choices'       =>  $roles ,
                    ])
            ->add( 'submit' , 'submit' , [ 'label'  =>  'upravit' ] )
        ;

        return $fb->getForm();
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createRegistrationForm()
    {
        $fb =   $this->app['form.factory']->createNamedBuilder( 'registration' , 'form' , new User() );
        $fb
            ->add( 'email' , 'email' ,
                    [
                        'constraints'   =>  [
                                                new Constraints\NotBlank() ,
                                                new Constraints\Email([ 'checkMX' => true ]) ,
                                                new Constraints\Callback(
                                                    function( $email , ExecutionContextInterface $context )
                                                    {
                                                        $user   =   $this->getUserRepository()->findOneBy([ 'email' => strtolower( $email ) ]);

                                                        if( $user )
                                                        {
                                                            $context->addViolation( "Email je již používán" );
                                                        }
                                                    }
                                                ) ,
                                            ] ,
                    ]
                 )
            ->add( 'username' , 'text' ,
                    [
                        'constraints'   =>  [
                                                new Constraints\NotBlank() ,
                                                new Constraints\Length([ 'min' => 3 , 'max' => '250' ]) ,
                                                new Constraints\Callback(
                                                    function( $username , ExecutionContextInterface $context )
                                                    {
                                                        $user   =   $this->getUserRepository()->findOneBy([ 'username' => strtolower( $username ) ]);

                                                        if( $user )
                                                        {
                                                            $context->addViolation( "Uživatelské jméno je již používáno" );
                                                        }
                                                    }
                                                ) ,
                                            ] ,
                    ]
                 )
            ->add( 'password' , 'repeated' ,
                    [
                        'type' => 'password' ,
                        'constraints'   =>  [
                                                new Constraints\NotBlank() ,
                                                new Constraints\Length([ 'min' => 5 , 'max' => '250' ])
                                            ] ,
                        'invalid_message' => 'Hesla se neshodují',
                        'first_options'  => array('label' => 'Heslo'),
                        'second_options' => array('label' => 'Heslo zopakujte'),
                    ]
                )
            ->add( 'submit' , 'submit' , [ 'label'  =>  'registrovat' ] )
        ;

        return $fb->getForm();
    }
}