<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use Utils\Request;

class ConnexionController
{
    public function displayFormSignUp()
    {
        $view = "formSignUp.phtml";
        include_once 'views/layout.phtml';
    }
   
    public function submitSignUp()
    {
        if( Request::hasValue( $_POST['bot_trap'] ) )
        {
            Request::redirect('notFound');
        }
        
        $userModel = new User();
        
        if( Request::hasValue( $_POST['firstname'] ) &&
            Request::hasValue( $_POST['lastname'] ) &&
            Request::hasValue( $_POST['mail'] ) &&
            Request::hasValue( $_POST['password'] )
            )
        {
            $errorMessages = [];
            
            if( mb_strlen( $_POST['lastname'] ) > 30 )
            {
                $errorMessages['lastnameError'][] = 'Le nom ne doit pas dépasser 30 caractères';
            }
            //-->pour le regex : "!" car sans, si le texte ne contient pas ce qui est indiqué:
            if (!preg_match("#^[\p{L}0-9-]+[\p{L}0-9- ]+$#", $_POST['lastname'] ) )
            {
                $errorMessages['lastnameError'][] = 'Au moins 2 caractères sans accent et caractères spéciaux non autorisés';
            }

            if( mb_strlen( $_POST['firstname'] ) > 30 )
            {
                $errorMessages['firstnameError'][] = 'Le prénom ne doit pas dépasser 30 caractères';
            }
            //-->pour le regex : "!" car sans, si le texte ne contient pas ce qui est indiqué:
            if ( !preg_match("#^[\p{L}0-9-]+[\p{L}0-9- ]+$#", $_POST['firstname'] ) )
            {
                $errorMessages['firstnameError'][] = 'Au moins 2 caractères sans accent et caractères spéciaux non autorisés';
            }

            $mail = filter_var( $_POST['mail'], FILTER_SANITIZE_EMAIL);
 
            if( !filter_var( $mail, FILTER_VALIDATE_EMAIL) )
            {
                $errorMessages['mailError'][] = 'Caractères spéciaux non autorisés';
            }
            else
            {
                $mail = $userModel->getUser(
                    $_POST['mail']
                    );
                if( $mail ) //-->si true
                {
                    $errorMessages['mailError'][] = 'Adresse mail déjà utilisée';
                }
            }

            if( ( $_POST['password'] ) !== ( $_POST['password2'] ) )
            {
                $errorMessages['passwordError'] = 'Les mots de passe ne correspondent pas !';
            }
            
            if( $errorMessages === [] )
            {
                $userModel->addUser([
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['mail'],
                    password_hash( $_POST['password'], PASSWORD_BCRYPT)
                ]);
                
                $lastUser = $userModel->getUser(
                    $_POST['mail']
                );
                
                if( Request::hasValue( $_SESSION['departureid'] ) &&
                    Request::hasValue( $_SESSION['arrivalid'] )
                    )
                {
                    $_SESSION['connectedUser'] = $lastUser;
                    Request::redirect('detailsItineraryUsers');
                }    
                    $_SESSION['connectedUser'] = $lastUser;
                    Request::redirect('userAccount');
            }
            else
            {
                $_SESSION['registerErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function displayformSignIn()
    {
        $view = "formSignIn.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function submitSignIn()
    {
        $userModel = new User();
        $user = $userModel->getUser(
            $_POST['mail']
        );
        
        if( !$user )
        {
            $_SESSION['signInErrorsMail'] = "Mauvaise adresse mail";
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }
        else
        {
            unset($_SESSION['signInErrorsMail']);
            
            if( password_verify( $_POST['password'], $user['password'] ) )
            {
                $_SESSION['connectedUser'] = $user;
                
                if( Request::hasValue( $_SESSION['departureid'] ) &&
                    Request::hasValue( $_SESSION['arrivalid'] )
                    )
                {
                    Request::redirect('detailsItineraryUsers');
                }

                Request::redirect('homeDestinations');
            }
            else
            {
                $_SESSION['signInErrorsPassword'] = "Mauvais mot de passe";
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function logout()
    {
        session_destroy();
        $_SESSION = [];
        
        Request::redirect('home');
    }
}