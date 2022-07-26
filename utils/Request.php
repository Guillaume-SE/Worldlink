<?php

namespace Utils;

class Request
{
    public static function isConnected()
    {
        return isset($_SESSION['connectedUser']);
    }
    
    public static function isAdmin()
    {
        return self::isConnected() && $_SESSION['connectedUser']['role'] === 'admin';
    }
    
    public static function isSuperAdmin()
    {
        return self::isConnected() && $_SESSION['connectedUser']['role'] === 'super_admin';
    }
    
    public static function hasValue( $value ) :bool
    {
        return isset( $value ) && !empty( $value);
    }
    
    public const PUBLIC_ROUTES = ['home', 'homeDestinations', 'filterDestinations','submitItineraryUsers',
                                  'detailsItineraryUsers', 'formSignIn', 'submitSignIn', 'formSignUp', 'submitSignUp',
                                  'logout', 'homeServices', 'homeHelp', 'notAvailable', 'notFound'];
    //juste être connecté
    public const PRIVATE_ROUTES = ['submitConfirmedItineraryUsers', 'detailsItineraryTickets', 'userAccount', 'informationsUser',
                                   'bookingsUser', 'detailsBookingUser','formCancelBooking', 'submitCancelBooking',
                                   'submitEditInformationsUser', 'submitEditPasswordUser', 'submitDeleteHisAccount'];
    
    public const ADMIN_ROUTES = ['pannelDestinations', 'detailsDescriptionDestination', 'searchDestinations', 'formAddDestinations',
                                 'submitAddDestinations', 'formEditDestinations', 'submitEditDestinations', 'formDeleteDestinations',
                                 'submitDeleteDestinations', 'detailsPictures','pannelBookings', 'searchBookings', 'pannelUsers', 
                                 'searchUsers', 'detailsUsers', 'formDeleteUsers', 'submitDeleteUsers'];
                                 
    public const SUPER_ADMIN_ROUTES = ['formEditUsersRole', 'submitEditRole'];
    
    public static function getAccessibleRoute( string $route ) :string
    {
        if( in_array( $route, self::PUBLIC_ROUTES ) )
        {
            return $route;
        }
        
        if( in_array( $route, Request::PRIVATE_ROUTES ) && !self::isConnected() )
        {
            return 'formSignIn';
        }
        
        if( in_array( $route, Request::ADMIN_ROUTES ) && !self::isAdmin() && !self::isSuperAdmin() )
        {
            return 'notFound';
        }
        
        if( in_array( $route, Request::SUPER_ADMIN_ROUTES ) && !self::isSuperAdmin() )
        {
            return 'notFound';
        }
    
        return $route;
    }

    public static function redirect (string $route) : void
    {
       header('location:index.php?route='.$route);
       exit;
    }
}