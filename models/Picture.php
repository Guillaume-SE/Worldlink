<?php

namespace Models;

class Picture extends Database
{
    public function getPictures()
    {
        $query = $this->getDb()->prepare(
            "SELECT 
                `id_picture`,
                `picture_name`,
                `file`, 
                `caption`
            FROM 
                `pictures`
            ORDER BY 
                `picture_name` ASC
            ");
        $query->execute();
        return $query->fetchAll();
    }
    
    public function getPictureById( int $id ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `id_picture`,
                `picture_name`,
                `file`, 
                `caption`
            FROM 
                `pictures`
            WHERE
                `id_picture` = ?
            ");
        $query->execute( [$id] );
        return $query->fetch();
    }
    
    public function getLastPicture() //dernière image add en bdd
    {
       $query = $this->getDb()->prepare(
           "SELECT 
                `id_picture`,
                `picture_name`,
                `file`, 
                `caption`
            FROM 
                `pictures` 
            ORDER BY 
                `id_picture` DESC 
            LIMIT 1
            ");
        $query->execute();
        return $query->fetch();
    }
    
    public function addPictures( array $pictureValues ) :void
    {
        $query = $this->getDb()->prepare(
            "INSERT INTO `pictures`
                (
                picture_name,
                file,
                caption
                )
            VALUES
                (
                ?,
                ?,
                ?
                )
            ");
        $query->execute( $pictureValues );
    }
    
    public function editPicture( array $pictureValues ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE
                `pictures`
            SET
                `picture_name` = ?,
                `file` = ?,
                `caption` = ?
            WHERE 
                `id_picture` = ?
            ");
        $query->execute( $pictureValues );
    }
    
    public function deletePicture( int $id ) :void
    {
         $query = $this->getDb()->prepare( 
            "DELETE FROM 
                `pictures`
            WHERE 
                `id_picture` = ?
            ");
        $query->execute( [$id] );
    }
}