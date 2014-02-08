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
        return $this->app->render( 'register.html.twig' , [ 'form' => $this->createRegistrationForm()->createView() ] );
    }

    public function postRegisterAction( Request $request )
    {
        $form   =   $this->createRegistrationForm();
        $form->submit( $request->request->get( 'form' ) );

        if( $form->isValid() )
        {
            $user   =   $form->getData();
            $em     =   $this->app['orm.em'];

            $em->persist( $user );
            $em->flush();

            return $this->app->render( 'registrationComplete.html.twig' , [ 'user' => $user ] );
        }
        else
        {
            return $this->app->render( 'register.html.twig' , [ 'form' => $form->createView() ] );
        }
    }

    public function getUserListAction()
    {
        $users  =   $this->getUserRepository()->findAll();

        return $this->app->render( 'userList.html.twig' , [ 'users' => $users ] );
    }

    protected function connect( \Silex\ControllerCollection $controllers )
    {
        $controllers->get( '/register', array( $this , 'getRegisterAction' ) )
            ->bind( 'get_register_user' )
        ;

        $controllers->post( '/register', array( $this , 'postRegisterAction' ) )
            ->bind( 'post_register_user' )
        ;

        $controllers->get( '/list', array( $this , 'getUserListAction' ) )
            ->bind( 'get_user_list' )
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

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createRegistrationForm()
    {
        $fb =   $this->app['form.factory']->createBuilder( 'form' , new User() );
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
            ->add( 'submit' , 'submit' )
        ;

        return $fb->getForm();
    }
}