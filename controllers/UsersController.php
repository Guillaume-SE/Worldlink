<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use DateTime;
use Utils\Request;

class UsersController
{
    public function displayInformationsUser()
    {
        $userModel = new User();
        $userInfos = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        
        $signUpDate = new DateTime($userInfos['registration_date']);
        $signUpDate = $signUpDate->format("d/m/Y à G:i");
        
        $view = "informationsUser.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function submitEditInformationsUser()
    {
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
            );
        
        if( Request::hasValue( $_POST['lastname'] ) &&
            Request::hasValue( $_POST['firstname'] ) &&
            Request::hasValue( $_POST['mail'] )
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
        
        if( $_POST['mail'] !== $currentUser['mail'] )
        {
            $mail = $userModel->getUser( //récupère les mails de la table mail
                $_POST['mail']
                    );
                
                if( $mail ) //-->si $mail contient une donnée, le mail existe en bdd
                {
                        $errorMessages['mailError'][] = 'Adresse mail déjà utilisée';
                }
                else
                {
                    $mail = filter_var( $_POST['mail'], FILTER_SANITIZE_EMAIL); // filtre les caractères interdits dans une adresse mail
                
                    if( !filter_var( $mail, FILTER_VALIDATE_EMAIL) ) // vérifie si le champ est différent d'un format d'adresse mail
                    {
                        $errorMessages['mailError'][] = 'Caractères spéciaux non autorisés';
                    }
                }
                
            }

            $verifPassword = password_verify( $_POST['password'], $currentUser['password']);
            if ( $verifPassword === false )
            {
                $errorMessages['currentPasswordError'] = 'Mauvais mot de passe';
            }
            
            if( $errorMessages === [] )
            {
                $userModel->editUserInformations([
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['mail'],
                    $currentUser['id_user']
                ]);
            
                $_SESSION['connectedUser'] = $currentUser;
                Request::redirect('userAccount');
            }
            else
            {
                $_SESSION['formEditInformationsUserErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function submitEditPasswordUser()
    {
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
            );
        
        if ( Request::hasValue( $_POST['new_password1'] ) )
        {
           $errorMessages = [];
           
            if( ( $_POST['new_password1'] ) !== ( $_POST['new_password2'] ) )
            {
               $errorMessages['newPasswordError'] = 'Les mots de passe ne correspondent pas !';
            }
           
           $verifPassword = password_verify( $_POST['password'], $currentUser['password']);
            if ( $verifPassword === false )
            {
                $errorMessages['currentPasswordError'] = 'Mauvais mot de passe';
            }
            
            if( $errorMessages === [] )
            {
                $userModel->editUserPassword([
                    password_hash( $_POST['new_password1'], PASSWORD_BCRYPT),
                    $currentUser['id_user']
                ]);
                
                $_SESSION['connectedUser'] = $currentUser;
                Request::redirect('userAccount');
            }
            else
            {
                $_SESSION['formEditPasswordUserErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function displayPannelUsers() //(backoffice)
    {
        $isSuperAdmin = Request::isSuperAdmin();
        $isAdmin = Request::isAdmin();

        $userModel = new User();
        $users = $userModel->getAllUsers();
        
        foreach ($users as $index => $user){
            $registrationDate = new DateTime($user['registration_date']);
            $registrationDate = $registrationDate->format("d/m/Y à G:i");
            
            $users[$index]['registration_date'] = $registrationDate;
        }
        
        $view = 'pannelUsers.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function ajaxSearchUsers() //ajax du pannelUsers
    {
        $search = $_GET['search'];
        
        $userModel = new User();
        $searchResults = $userModel->getUserBySearch(
            htmlspecialchars( $search )
            );
        $isAdmin = Request::isAdmin();
        $isSuperAdmin = Request::isSuperAdmin();
        $data = ['isSuperAdmin' => $isSuperAdmin, 'isAdmin' => $isAdmin, 'results' => $searchResults];
        
        echo json_encode( $data );
    }
    
    public function displayDetailsUsers()
    {
        $userModel = new User();
        $user = $userModel->getUserById(
            $_GET['userid']
        );
        
        $registrationDate = new DateTime($user['registration_date']);
        $registrationDate = $registrationDate->format("d/m/Y à G:i");
        
        $bookingModel = new Booking();
        $listBookingsUsers  = $bookingModel->getUsersBookings(
            $_GET['userid']
        );
        
        foreach ($listBookingsUsers as $index => $booking){
            $bookingDate = new DateTime($booking['booking_date']);
            $bookingDate = $bookingDate->format("d/m/Y à G:i");
            
            $travelDate = new DateTime($booking['travel_date']);
            $travelDate = $travelDate->format("d/m/Y à G:i");
            
            $listBookingsUsers[$index]['booking_date'] = $bookingDate;
            $listBookingsUsers[$index]['travel_date'] = $travelDate;
        }
        
        $view = 'detailsUsers.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function displayFormEditUsersRole()
    {
        $userModel = new User();
        $user = $userModel->getUserById(
            $_GET['userid']
            );
        $view = 'formEditUsersRole.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function submitEditRole()
    {

        $userModel = new User();
        
        if( Request::hasValue( $_POST['role'] ) )
        {
            $errorMessages = [];
            
            $roleList = ['user','admin','super_admin'];
                
            if( !in_array( $_POST['role'], $roleList ) ) //-->vérifie que le POST correspond à une donnée du tableau $roleList
            {
                $errorMessages['roleError'][] = 'Le rôle ne correspond pas à la sélection.';
            }
            else
            {
                $role = $userModel->getUserByRole(
                    $_GET['userid']
                    );
                
                if( $role['role'] === $_POST['role'] )
                {
                    $errorMessages['roleError'][] = 'L\'utilisateur a déjà ce rôle';
                }
            }
            
            if( $errorMessages === [] )
            {
                $user = $userModel->editRoleUser([
                    $_POST['role'],
                    $_GET['userid']
                    ]);
                
                Request::redirect('pannelUsers');
            }
            else
            {
                $_SESSION['formEditRoleErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function submitDeleteHisAccount()
    {
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        
        if ( Request::hasValue( $_POST['password1'] ) )
        {
            $errorMessages = [];
            
            $verifPassword = password_verify( $_POST['password1'], $currentUser['password']);
            
            if ( $verifPassword === false )
            {
                $errorMessages['currentPasswordError'][] = 'Mauvais mot de passe';
            }
            
            if( $_POST['password1'] !== $_POST['password2'] )
            {
                $errorMessages['currentPasswordError'][] = 'Les mots de passe ne correspondent pas !';
            }
            
            if( Request::hasValue( $_POST['confirm'] ) )
            {
                $confirm = 1;
            }
            else
            {
                $confirm = 0;
            }
            
            if( $confirm !== 1 )
            {
                $errorMessages['confirmError'] = 'Veuillez cocher cette case';
            }
            
            if( $errorMessages === [] )
            {
                $user = $userModel->deleteUser(
                    $currentUser['id_user']    
                );
            
            session_destroy();
            Request::redirect('home');
            }
            else
            {
                $_SESSION['formDeleteHisAccountErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
    
    public function displayFormDeleteUsers()
    {
        $userModel = new User();
        $userDetails = $userModel->getUserById(
            $_GET['userid']
        );
        
        if( Request::isSuperAdmin() || Request::isAdmin() && $userDetails['role'] === 'user' )
        {
            $view = 'formDeleteUsers.phtml';
            include_once 'views/layout.phtml';
        }
        else
        {
            Request::redirect('pannelUsers');
        }
    }
    
    public function submitDeleteUsers()
    {
        $userModel = new User();
        $errorMessage = [];
        
        if( Request::hasValue( $_POST['check_delete'] ) )
        {
            $checkDelete = 1;
        }
        else
        {
            $checkDelete = 0;
        }
            
        if( $checkDelete !== 1 )
        {
            $errorMessage['checkDeleteUserError'] = 'Veuillez cocher cette case';
        }
            
        if( $errorMessage === [] )
        {
            $userModel->deleteUser(
                $_GET['userid']
            );
            
            Request::redirect('pannelUsers');
        }
        else
            $_SESSION['formDeleteUserError'] = $errorMessage;
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
    }    
}