<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use DateTime;
use Utils\Request;

class BookingsController
{
    public function displayPannelBookings() //-->resa backoffice
    {
        $bookingModel = new Booking();
        $bookings = $bookingModel->getBookings();
        
        foreach ($bookings as $index => $booking){
            $bookingDate = new DateTime($booking['booking_date']);
            $bookingDate = $bookingDate->format("d/m/Y à G:i");
            
            $travelDate = new DateTime($booking['travel_date']);
            $travelDate = $travelDate->format("d/m/Y à G:i");
            
            $bookings[$index]['booking_date'] = $bookingDate;
            $bookings[$index]['travel_date'] = $travelDate;
        }
        
        $view = 'pannelBookings.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function ajaxSearchBookings() //-->ajax de pannelBookings
    {
        $search = $_GET['search'];
            
        $bookingModel = new Booking();
        $searchResults = $bookingModel->getBookingsBySearch(
            htmlspecialchars( $search )
            );
            
        echo json_encode( $searchResults );
    }
    
    public function displayBookingsUser()
    {
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        $bookingModel = new Booking();
        $userBookings = $bookingModel->getUsersBookings(
            $currentUser['id_user']
        );
        
        foreach ($userBookings as $index => $booking){
            $bookingDate = new DateTime($booking['booking_date']);
            $bookingDate = $bookingDate->format("d/m/Y à G:i");
            $userBookings[$index]['booking_date'] = $bookingDate;
        }
        
        $view = "bookingsUser.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function displayDetailsBookingUser()
    {
        if( $_GET['bookingid'] === null )
        {
            Request::redirect('notFound');
        }
        
        $bookingModel = new Booking();
        $userBookingDetails = $bookingModel->getBookingById(
            $_GET['bookingid']    
        );
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        
        $travelDate = new DateTime($userBookingDetails['travel_date']);
        $travelDate = $travelDate->format("d/m/Y à G:i");
        
        $bookingDate = new DateTime($userBookingDetails['booking_date']);
        $bookingDate = $bookingDate->format("d/m/Y à G:i");
        
        
        if( $userBookingDetails['user_id'] === $currentUser['id_user'] && $userBookingDetails['user_id'] !== null )
        {
            $view = "detailsBookingUser.phtml";
            include_once 'views/layout.phtml';
        }
        else
        {
            Request::redirect('bookingsUser');
        }
    }
    
    public function displayFormCancelBooking()
    {
        $bookingModel = new Booking();
        $userBookingDetails = $bookingModel->getBookingById(
            $_GET['bookingid']    
        );
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        
        if( $userBookingDetails['user_id'] === $currentUser['id_user'] && $userBookingDetails['status'] === "Confirmée" )
        {
            $view = "formCancelBooking.phtml";
            include_once 'views/layout.phtml';
        }
        else
        {
            Request::redirect('bookingsUser');
        }
    }
    
    public function submitCancelBooking()
    {
        $idBooking = $_GET['bookingid'];
        $bookingModel = new Booking();
        $bookingDetails = $bookingModel->getBookingById(
            $idBooking 
        );
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        
        if( $bookingDetails['user_id'] === $currentUser['id_user'] )
        {
            $errorMessage = [];
            $newStatus = "Remboursée";
            
            if( Request::hasValue( $_POST['check_cancel'] ) )
            {
                $checkCancel = 1;
            }
            else
            {
                $checkCancel = 0;
            }
            
            if( $checkCancel !== 1 )
            {
                $errorMessage['checkCancelError'] = 'Veuillez cocher cette case';
            }
            
            if( $errorMessage === [] )
            {
                $booking = $bookingModel->cancelBooking([
                    $newStatus,
                    $bookingDetails['id_booking']
                ]);
                
                Request::redirect('bookingsUser');
            }
            else
            {
                $_SESSION['formCancelBookingError'] = $errorMessage;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
        }
        else
        {
            Request::redirect('bookingsUser');
        }
    }
    
    public function submitConfirmedItineraryUsers()
    {
        $userModel = new User();
        $currentUser = $userModel->getUserById(
            $_SESSION['connectedUser']['id_user']
        );
        $destinationModel = new Destination();
        $departureValues = $destinationModel->getDestinationById(
            $_SESSION['departureid']
        );
        $arrivalValues = $destinationModel->getDestinationById(
            $_SESSION['arrivalid']
        );
        $bookingModel = new Booking();
        
        if( Request::hasValue( $_POST['travel_date'] ) &&
            Request::hasValue( $_POST['travel_time'] )
            )
        {
            $errorMessage = []; 
            $datetimePost = $_POST['travel_date']. " " .$_POST['travel_time'];
            
            try
            {
                $date = new DateTime( $datetimePost );
            }
            catch(\Exception $e )
            {
                $errorMessage[] = 'Date ou horaire invalide';
                $_SESSION['formConfirmedItineraryUserErrors'] = $errorMessage;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
            
            $travelDate = $datetimePost;
            $departureLocation = $departureValues['city']. " (" .$departureValues['country']. ")";
            $arrivalLocation = $arrivalValues['city']. " (" .$arrivalValues['country']. ")";
            $totaPrice = $departureValues['price'] + $arrivalValues['price'];
            
            if( $errorMessage === [] )
            {
                $bookingModel->addUsersBookings([
                    $currentUser['id_user'],
                    $travelDate,
                    $departureLocation,
                    $arrivalLocation,
                    $totaPrice
            ]);
            
                Request::redirect('detailsItineraryTickets');
            }
        }
    }

    public function displayDetailsItineraryTickets()
    {
        $bookingModel = new Booking();
        $bookingValues = $bookingModel->getLastUserBooking(
            $_SESSION['connectedUser']['id_user']
        );
        
        $bookingDate = new DateTime($bookingValues['booking_date']);
        $bookingDate = $bookingDate->format("d/m/Y à G:i");
        
        $travelDate = new DateTime($bookingValues['travel_date']);
        $travelDate = $travelDate->format("d/m/Y à G:i");
        
        $view = 'detailsItineraryTickets.phtml';
        include_once 'views/layout.phtml';
    }
}