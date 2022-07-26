<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use Utils\Request;

class PicturesController
{
    public function displayDetailsPictures() // pour pannelDestinations
    {
        $pictureModel = new Picture();
        $picture = $pictureModel->getPictureById(
            $_GET['pictureid']
            );
        $view = 'detailsPictures.phtml';
        include_once 'views/layout.phtml';
    }
}