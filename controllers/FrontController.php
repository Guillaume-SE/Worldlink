<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use Utils\Request;

class FrontController
{
    public function displayHomePage()
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getDestinations();
        $view = 'home.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function userAccount()
    {
        $isAdminOrSuperAdmin = Request::isAdmin() || Request::isSuperAdmin();
        
        $userModel = new User();
        $userInfos = $userModel->getUserById(
        $_SESSION['connectedUser']['id_user']
        );
        
        $view = "userAccount.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function displayHomeDestinations()
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getDestinationsWithPictures();
        $view = 'homeDestinations.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function ajaxFilterDestinations() //-->ajax de homeDestinations
    {
        $search = $_GET['search'];
            
        $destinationModel = new Destination();
        $searchResults = $destinationModel->getDestinationsWithPicturesBySearch(
            htmlspecialchars( $search )
            );
            
        echo json_encode( $searchResults );
    }
    
    public function submitItineraryUsers()
    {
        if( Request::hasValue( $_POST['departure'] ) &&
            Request::hasValue( $_POST['arrival'] )
            )
        {
            if ( $_POST['departure'] === $_POST['arrival'] )
            {
                $_SESSION['submitItineraryErrors'] = 'Impossible de voyager vers une même ville';
                header( 'Location: '.$_SERVER['HTTP_REFERER'] );
                exit();
            }
            
                $idDeparture = $_POST['departure'];
                $idArrival   = $_POST['arrival'];
                
                unset($_SESSION['submitItineraryErrors']);
                $_SESSION['departureid'] = $idDeparture;
                $_SESSION['arrivalid'] = $idArrival;
                Request::redirect('detailsItineraryUsers');
        }
    }
    
    public function displayDetailsItineraryUsers()
    {
        $departure = $_SESSION['departureid'];
        $arrival   = $_SESSION['arrivalid'];
        
        if ( $departure == null && $arrival == null )
        {
            Request::redirect('homeDestinations');
        }
        
        $isConnected = Request::isConnected();
        $destinationModel = new Destination();
        $departureValues = $destinationModel->getDestinationsWithPicturesById(
            $departure
            );
        $arrivalValues = $destinationModel->getDestinationsWithPicturesById(
            $arrival
            );
        $totalPrice = $departureValues['price'] + $arrivalValues['price'];
        
        $view = 'detailsItineraryUsers.phtml';
        include_once 'views/layout.phtml'; // submit dans BookingsController
    }
    
    public function displayHomeServices()
    {
        $view = 'homeServices.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function displayHomeHelp()
    {
        $view = 'homeHelp.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function notAvailable()
    {
        $view = 'notAvailable.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function notFound()
    {
        $view = 'notFound.phtml';
        include_once 'views/layout.phtml';
    }
}