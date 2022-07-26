<?php

namespace Models;

class User extends Database
{
    public function getUser( string $mail) :array
    {
        $query = $this->getDb()->prepare( 
            "SELECT
                `id_user`,
                `mail`, 
                `password`, 
                `firstname`, 
                `lastname`,
                `registration_date`,
                `role`
            FROM 
                `users`
            WHERE 
                `mail` = ?
            ");
        $query->execute( [$mail] );
        $result = $query->fetch();

        if( $result === false ) {
            return []; // si result est un boolean false on renvoie un tableau vide
       }
       return $result; //sinon 
    }
    
    public function getAllUsers()
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `id_user`,
                `firstname`,
                `lastname`,
                `mail`,
                `password`,
                `registration_date`,
                `role`
            FROM
                `users`
            ORDER BY
                `lastname`,
                `firstname` ASC
            ");
        $query->execute();
        return $query->fetchAll();
    }
    
    public function getUserById( int $id ) :array
    {
        $query = $this->getDb()->prepare( 
            "SELECT 
                `id_user`,
                `firstname`, 
                `lastname`,
                `mail`, 
                `password`,
                `registration_date`,
                `role`
            FROM 
                `users`
            WHERE 
                `id_user` = ?
            ");
        $query->execute( [$id] );
        return $query->fetch();
    }
    
    public function getUserByRole( string $role ) :array
    {
        $query = $this->getDb()->prepare(
            "SELECT
                `role`
            FROM
                `users`
            WHERE 
                `id_user` = ? 
            ");
        $query->execute( [$role] );
        return $query->fetch();
    }
    
    public function getUserBySearch( string $values ) :array //ajax
    {
        $query = $this->getDb()->prepare( 
            "SELECT 
        	    `id_user`,
                `firstname`,
                `lastname`,
                `mail`,
                `password`,
                `registration_date`,
                `role`
        	FROM 
                `users`
        	WHERE 
        	    `id_user` LIKE ? 
        	OR  `firstname` LIKE ? 
        	OR  `lastname` LIKE ? 
        	OR  `mail` LIKE ? 
        	OR  `role` LIKE ?
        	ORDER BY 
        	    `lastname`, 
        	    `firstname` ASC
        	" );
        $query->execute(["$values%", "$values%", "$values%", "$values%", "$values%"]);
        return $query->fetchAll();
    }
    
    public function addUser( array $userValues ) :void
    {
        $query = $this->getDb()->prepare(
        "INSERT INTO 
                `users`
                (
                firstname,
                lastname,
                mail,
                password,
                registration_date
                ) 
        VALUES 
            ( 
            ?, 
            ?, 
            ?, 
            ?, 
            NOW() 
            )
            ");
        $query->execute( $userValues );
    }
    
    public function editUserInformations( array $userValues ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE
                `users`
            SET
                `firstname` = ?,
                `lastname` = ?,
                `mail` = ?
            WHERE
                `id_user` = ?
            ");
        $result = $query->execute( $userValues );
    }
    
    public function editUserPassword( array $userPassword ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE
                `users`
            SET
                `password` = ?
            WHERE
                `id_user` = ?
            ");
        $result = $query->execute( $userPassword );
    }
    
    public function editRoleUser( array $role ) :void
    {
        $query = $this->getDb()->prepare(
            "UPDATE
                `users`
            SET
                `role` = ?
            WHERE
                `id_user` = ?
            ");
        $query->execute( $role );
    }
    
    public function deleteUser( int $id ) :void
    {
        $query = $this->getDb()->prepare(
            "DELETE FROM
                `users`
            WHERE
                `id_user` = ?
            ");
        $query->execute( [$id] );
    }
}