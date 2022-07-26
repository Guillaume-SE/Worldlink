<?php

namespace Models;

class Booking extends Database
{
    public function getBookings()
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `id_booking`,
                `user_id`,
                `booking_date`,
                `travel_date`,
                `destination_departure`,
                `destination_arrival`,
                `status`,
                `total_price`
            FROM
                `bookings`
            ORDER BY
                `id_booking` DESC
            ");
        $query->execute();
        return $query->fetchAll();
    }
    
    public function getBookingById( int $id ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                bookings.`id_booking`,
                bookings.`user_id`,
                users.`lastname`,
                users.`firstname`,
                bookings.`booking_date`,
                bookings.`travel_date`,
                bookings.`destination_departure`,
                bookings.`destination_arrival`,
                bookings.`status`,
                bookings.`total_price`
            FROM
                `bookings`
            INNER JOIN
                `users`
            ON
                users.id_user = bookings.user_id
            WHERE
                bookings.`id_booking` = ?
            ");
        $query->execute( [$id] );
        return $query->fetch();
    }
    
    public function getUsersBookings( int $id_user) :array
    {
        $query = $this->getDb()->prepare(
                "SELECT 
                    `id_booking`,
                    `booking_date`,
                    `travel_date`,
                    `destination_departure`,
                    `destination_arrival`,
                    `status`,
                    `total_price`
                FROM 
                    `bookings`
                WHERE 
                    `user_id` = ?
                ORDER BY 
                    `id_booking` DESC
            ");
        $query->execute( [$id_user] );
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getBookingsBySearch( string $value ) :array // Ajax backoffice
    {
        $query = $this->getDb()->prepare( 
            "SELECT 
                `id_booking`,
                `user_id`,
                `booking_date`,
                `travel_date`,
                `destination_departure`,
                `destination_arrival`,
                `status`,
                `total_price`
            FROM 
                `bookings`
            WHERE
                `id_booking` LIKE ?
            OR  `user_id` LIKE ? 
            OR  `booking_date` LIKE ? 
            OR  `travel_date` LIKE ?
            OR  `destination_departure` LIKE ?
            OR  `destination_arrival` LIKE ?
            OR  `status` LIKE ?
            ORDER BY 
                `id_booking` DESC
        	" );
        $query->execute( ["$value%", "$value%", "$value%", "$value%", "$value%", "$value%", "$value%"] );
        return $query->fetchAll();
    }
    
    public function getLastUserBooking( int $id ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                bookings.`id_booking`,
                users.`lastname`,
                users.`firstname`,
                bookings.`booking_date`,
                bookings.`travel_date`,
                bookings.`destination_departure`,
                bookings.`destination_arrival`,
                bookings.`status`,
                bookings.`total_price`
            FROM
                `bookings`
            INNER JOIN
                `users`
            ON
                users.id_user = bookings.user_id
            WHERE
                bookings.`user_id` = ?
            ORDER BY
                bookings.`id_booking` DESC
            LIMIT 1
            ");
        $query->execute( [ $id ] );
        $result = $query->fetch();

        if( $result === false )
        {
            return [];
        }
       return $result;
    }

    public function addUsersBookings( array $bookingValues ) :void
    {
        $query = $this->getDb()->prepare(
            "INSERT INTO `bookings`
                (
                user_id,
                booking_date,
                travel_date,
                destination_departure,
                destination_arrival,
                total_price
                )
            VALUES
                (
                ?,
                NOW(),
                ?,
                ?,
                ?,
                ?
                )
            ");
        $query->execute( $bookingValues );
    }
    
    public function cancelBooking( array $status ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE
                `bookings`
            SET
                `status` = ?
            WHERE
                `id_booking` = ?
            ");
    
        $query->execute( $status );
    }
    
}