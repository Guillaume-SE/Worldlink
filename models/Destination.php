<?php

namespace Models;

class Destination extends Database
{
    
    public function getDestinations()
    {
       $query = $this->getDb()->prepare(
           "SELECT 
                `id_destination`,
                `country`,
                `city`, 
                `world_area`, 
                `description`,
                `price`,
                `picture_id`,
                `status`,
                `registration_date`,
                `is_available`
            FROM 
                `destinations`
            ORDER BY 
                `country`, `city` ASC
            ");
        $query->execute();
        return $query->fetchAll();
    }
    
    public function getDestinationsWithPictures()
    {
        $query = $this->getDb()->prepare(
           "SELECT 
                destinations.`id_destination`,
                destinations.`country`,
                destinations.`city`, 
                destinations.`world_area`, 
                destinations.`description`,
                destinations.`price`,
                pictures.`file`,
                pictures.`caption`,
                destinations.`status`,
                destinations.`registration_date`,
                destinations.`is_available`
            FROM 
                `destinations`
            INNER JOIN 
                `pictures`
            ON 
                pictures.id_picture = destinations.picture_id
            ORDER BY 
                RAND ()
            ");
        $query->execute();
        return $query->fetchAll();
    }
    public function getDestinationsWithPicturesById( int $id ) :array
    {
        $query = $this->getDb()->prepare(
           "SELECT 
                destinations.`id_destination`,
                destinations.`country`,
                destinations.`city`, 
                destinations.`world_area`, 
                destinations.`description`,
                destinations.`price`,
                pictures.`file`,
                pictures.`caption`,
                destinations.`status`,
                destinations.`registration_date`,
                destinations.`is_available`
            FROM 
                `destinations`
            INNER JOIN 
                `pictures`
            ON 
                pictures.id_picture = destinations.picture_id
            WHERE 
                destinations.id_destination = ?
            ");
        $query->execute( [$id] );
        return $query->fetch();
    }
    
    public function getDestinationById( int $id ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `id_destination`,
                `country`,
                `city`, 
                `world_area`, 
                `description`,
                `price`,
                `picture_id`,
                `status`,
                `registration_date`,
                `is_available`
            FROM
                `destinations`
            WHERE
                `id_destination` = ?
            ");
        $query->execute( [$id] );
        return $query->fetch();
    }
    
    public function getDestinationByCity( string $city ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `id_destination`,
                `country`,
                `city`, 
                `world_area`, 
                `description`,
                `price`,
                `picture_id`,
                `status`,
                `registration_date`,
                `is_available`
            FROM
                `destinations`
            WHERE 
                `city` = ? 
            ");
        $query->execute( [$city] );
        $result = $query->fetch();

        if( $result === false ) {
            return []; // si result est un boolean false on renvoie un tableau vide
       }
       return $result; //sinon on renvoie le tableau avec les résultat
    }
    
    public function getDestinationsWithPicturesBySearch( string $value ) :array // pour AJAX de destinations front
    {
        $query = $this->getDb()->prepare( 
            "SELECT 
                destinations.`id_destination`,
                destinations.`country`,
                destinations.`city`, 
                destinations.`world_area`, 
                destinations.`description`,
                destinations.`price`,
                pictures.`file`,
                pictures.`caption`,
                destinations.`status`,
                destinations.`registration_date`,
                destinations.`is_available`
            FROM 
                `destinations`
            INNER JOIN 
                `pictures`
            ON 
                pictures.id_picture = destinations.picture_id
            WHERE
                destinations.`city` LIKE ? 
            OR  destinations.`country` LIKE ? 
            OR  destinations.`world_area` LIKE ?
            ORDER BY 
                destinations.`country`,
                destinations.`city` ASC
        	" );
        $query->execute( ["$value%", "$value%", "$value%"] );
        return $query->fetchAll();
    }
    
    public function getDestinationBySearch( string $value ) :array // pour Ajax de destinations backoffice
    {
        $query = $this->getDb()->prepare( 
            "SELECT 
                `id_destination`,
                `country`,
                `city`, 
                `world_area`, 
                `description`,
                `price`,
                `picture_id`,
                `status`,
                `registration_date`,
                `is_available`
            FROM 
                `destinations`
            WHERE
                `id_destination` LIKE ?
            OR  `city` LIKE ? 
            OR  `country` LIKE ? 
            OR  `world_area` LIKE ?
            OR  `status` LIKE ?
            ORDER BY 
                `country`,
                `city` ASC
        	" );
        $query->execute( ["$value%", "$value%", "$value%", "$value%", "$value%"] );
        return $query->fetchAll();
    }
    
    public function addDestinations( array $destinationValues ) :void
    {
        $query = $this->getDb()->prepare(
            "INSERT INTO `destinations`
                (
                country,
                city,
                world_area,
                description,
                price,
                picture_id,
                status,
                registration_date,
                is_available
                )
            VALUES 
                (
                ?,
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                NOW(), 
                ?
                )
            ");
        $query->execute( $destinationValues );
    }
    
    public function editDestination( array $destinationValues ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE 
                `destinations`
            SET
                `country` = ?,
                `city` = ?,
                `world_area` = ?,
                `description` = ?,
                `price` = ?,
                `status` = ?,
                `is_available` = ?
            WHERE 
                `id_destination` = ?
            ");
        $query->execute( $destinationValues );
    }
    
    public function deleteDestination( int $id ) :void
    {
        $query = $this->getDb()->prepare(
            "DELETE FROM 
                `destinations`
            WHERE 
                `id_destination` = ?
            ");
        $query->execute( [$id] ); 
    }
}