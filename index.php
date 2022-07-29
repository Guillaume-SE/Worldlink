<?php
session_start();

use Utils\Request;

spl_autoload_register( function( $class ){
    require_once lcfirst( str_replace( '\\', '/', $class ) ) . '.php';
});

if( array_key_exists( 'route', $_GET ) )
{
    $route = Request::getAccessibleRoute( $_GET['route'] );
    switch( $route )
    {
        /***
        Accueil
        ***/
        case 'home':
            $controller = new Controllers\FrontController();
            $controller->displayHomePage();
           break;
        /***
        Destinations
        ***/
        case 'homeDestinations':
            $controller = new Controllers\FrontController();
            $controller->displayHomeDestinations();
            break;
        case 'filterDestinations':
            $controller = new Controllers\FrontController();
            $controller->ajaxFilterDestinations();
            break;
        case 'submitItineraryUsers':
            $controller = new Controllers\FrontController();
            $controller->submitItineraryUsers();
            break;
        case 'detailsItineraryUsers':
            $controller = new Controllers\FrontController();
            $controller->displayDetailsItineraryUsers();
            break;
        case 'submitConfirmedItineraryUsers':
            $controller = new Controllers\BookingsController();
            $controller->submitConfirmedItineraryUsers();
            break;
        case 'detailsItineraryTickets':
            $controller = new Controllers\BookingsController();
            $controller->displayDetailsItineraryTickets();
            break;
        /*Gestion admin des destinations*/
        case 'pannelDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->displayPannelDestinations();
            break;
        case 'searchDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->ajaxSearchDestinations();
            break;
        case 'detailsDescriptionDestination':
            $controller = new Controllers\DestinationsController();
            $controller->displayDetailsDescriptionDestination();
            break;
        case 'formAddDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->displayFormAddDestinations();
            break;
        case 'submitAddDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->submitAddDestinations();
            break;
        case 'formEditDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->displayFormEditDestinations();
            break;
        case 'submitEditDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->submitEditDestinations();
            break;
        case 'formDeleteDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->displayFormDeleteDestinations();
            break;
        case 'submitDeleteDestinations':
            $controller = new Controllers\DestinationsController();
            $controller->submitDeleteDestinations();
            break;
        /*Gestion admin des photos de destinations*/
         case 'detailsPictures':
            $controller = new Controllers\PicturesController();
            $controller->displayDetailsPictures();
            break;
        /*Gestion admin des réservations*/
        case 'pannelBookings':
            $controller = new Controllers\BookingsController();
            $controller->displayPannelBookings();
            break;
        case 'searchBookings':
            $controller = new Controllers\BookingsController();
            $controller->ajaxSearchBookings();
            break;
        /***
        Services
        ***/
        case 'homeServices':
            $controller = new Controllers\FrontController();
            $controller->displayHomeServices();
            break;
        /***
        Aide
        ***/
        case 'homeHelp':
            $controller = new Controllers\FrontController();
            $controller->displayHomeHelp();
            break;
        /***
        Compte utilisateur
        ***/
        case 'formSignUp':
            $controller = new Controllers\ConnexionController();
            $controller->displayFormSignUp();
            break;
        case 'submitSignUp':
            $controller = new Controllers\ConnexionController();
            $controller->submitSignUp();
            break;
        case 'formSignIn':
            $controller = new Controllers\ConnexionController();
            $controller->displayFormSignIn();
           break;
        case 'submitSignIn':
            $controller = new Controllers\ConnexionController();
            $controller->submitSignIn();
            break;
        case 'logout':
            $controller = new Controllers\ConnexionController();
            $controller->logout();
            break;
        /*Gestion de son compte*/
        case 'userAccount':
            $controller = new Controllers\FrontController();
            $controller->userAccount();
            break;
        case 'informationsUser':
            $controller = new Controllers\UsersController();
            $controller->displayInformationsUser();
            break;
        case 'bookingsUser':
            $controller = new Controllers\BookingsController();
            $controller->displayBookingsUser();
            break;
        case 'detailsBookingUser':
            $controller = new Controllers\BookingsController();
            $controller->displayDetailsBookingUser();
            break;
        case 'formCancelBooking':
            $controller = new Controllers\BookingsController();
            $controller->displayFormCancelBooking();
            break;
        case 'submitCancelBooking':
            $controller = new Controllers\BookingsController();
            $controller->submitCancelBooking();
            break;
        case 'submitEditInformationsUser':
            $controller = new Controllers\UsersController();
            $controller->submitEditInformationsUser();
            break;
        case 'submitEditPasswordUser':
            $controller = new Controllers\UsersController();
            $controller->submitEditPasswordUser();
            break;
        case 'submitDeleteHisAccount':
            $controller = new Controllers\UsersController();
            $controller->submitDeleteHisAccount();
            break;
        /*Gestion admin des utilisateurs*/
        case 'pannelUsers':
            $controller = new Controllers\UsersController();
            $controller->displayPannelUsers();
            break;
        case 'searchUsers':
            $controller = new Controllers\UsersController();
            $controller->ajaxSearchUsers();
            break;
        case 'detailsUsers':
            $controller = new Controllers\UsersController();
            $controller->displayDetailsUsers();
            break;
        case 'formEditUsersRole':
            $controller = new Controllers\UsersController();
            $controller->displayFormEditUsersRole();
            break;
        case 'submitEditRole':
            $controller = new Controllers\UsersController();
            $controller->submitEditRole();
            break;
        case 'formDeleteUsers':
            $controller = new Controllers\UsersController();
            $controller->displayFormDeleteUsers();
            break;
        case 'submitDeleteUsers':
            $controller = new Controllers\UsersController();
            $controller->submitDeleteUsers();
            break;
        /***
        Page non trouvée
        ***/
        case 'notAvailable':
            $controller = new Controllers\FrontController();
            $controller->notAvailable();
            break;
        case 'notFound':
            $controller = new Controllers\FrontController();
            $controller->notFound();
            break;
    }
}
else
{
    header( 'Location: index.php?route=home' );
    exit();
}
