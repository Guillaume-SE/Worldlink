<?php

namespace Controllers;
use Models\Destination;
use Models\Picture;
use Models\Booking;
use Models\User;
use DateTime;
use Utils\Request;

class DestinationsController
{
    public function displayPannelDestinations()
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getDestinations();
        
        foreach ($destinations as $index => $destination){
            $registrationDate = new DateTime($destination['registration_date']);
            $registrationDate = $registrationDate->format("d/m/Y à G:i");
            
            $destinations[$index]['registration_date'] = $registrationDate;
        }
        
        $view = 'pannelDestinations.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function displayDetailsDescriptionDestination()
    {
        $destinationModel = new Destination();
        $destinationDescription = $destinationModel->getDestinationById(
            $_GET['destinationid']
        );
        
        $view = 'detailsDescriptionDestination.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function ajaxSearchDestinations() //-->ajax pannelDestinations
    {
        $search = $_GET['search'];
        
        $destinationModel = new Destination();
        $searchResults = $destinationModel->getDestinationBySearch(
            htmlspecialchars( $search )
            );
        
        echo json_encode( $searchResults );
    }
    
    public function displayFormAddDestinations()
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getDestinations();
        $view = "formAddDestinations.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function submitAddDestinations()
    {
        $destinationModel = new Destination();
        
        if( Request::hasValue( $_POST['country'] ) &&
            Request::hasValue( $_POST['city'] ) &&
            Request::hasValue( $_POST['world_area'] ) &&
            Request::hasValue( $_POST['description'] ) &&
            Request::hasValue( $_POST['caption'] ) &&
            Request::hasValue( $_POST['status'] )
            )
        {
            $errorMessages = [];
            
            if( strlen( $_POST['country'] ) > 50 )
            {
                $errorMessages['countryError'][] = 'Le pays ne doit pas dépasser 50 caractères.';
            }

            if( strlen( $_POST['city'] ) > 60 )
            {
                $errorMessages['cityError'][] = 'La ville ne doit pas dépasser 60 caractères.';
            }
            else
            {
                $city = $destinationModel->getDestinationByCity(
                    $_POST['city']
                    );
                
                if( $city ) //-->si true, ville existe déjà en bdd
                {
                    $errorMessages['cityError'][] = 'Cette ville a déjà une fiche.';
                }
            }
 
            if( strlen( $_POST['world_area'] ) > 20 )
            {
                $errorMessages['world_areaError'][] = 'Le continent ne doit pas dépasser 20 caractères.';
            }
                $worldAreaList = ['Europe', 'Asie', 'Amérique du Nord', 'Amérique du Sud', 'Afrique', 'Océanie', 'Antarctique', 'Inconnu' ];
            
            if( !in_array( $_POST['world_area'], $worldAreaList ) ) //-->vérifie que le champs saisi correspond au valeurs dans le tableau $worldArea:
            {
                $errorMessages['world_areaError'][] = 'Le continent ne correspond pas à la sélection.';
            }

            $price = ( random_int(100, 1000) );

            if( isset( $_FILES['file'] ) && $_FILES['file']['error'] != 0 )
            {
                //-->erreur liée à FILE_UPLOAD_ERROR:
                switch( $_FILES['file']['error'] )
                {
                    case 1:
                        $errorMessages['fileError'][] = 'La taille du fichier dépasse la limite autorisée par le serveur (max: 2Mo).'; //-->case 1 lié à php.ini
                        break;
                    case 3:
                        $errorMessages['fileError'][] = 'Le fichier téléchargé n\'a été que partiellement téléchargé.';
                        break;
                    case 4:
                        $errorMessages['fileError'][] = 'Aucun fichier n\'a été téléchargé.';
                        break;
                    case 6:
                        $errorMessages['fileError'][] = 'Il y a un soucis de notre côté, veuillez réesayer plus tard.';
                        break;
                    case 7:
                        $errorMessages['fileError'][] = 'Il y a un soucis de notre côté, veuillez réesayer plus tard.';
                        break;
                    case 8:
                        $errorMessages['fileError'][] = 'Erreur inconnue';
                        break;
                }
            }
            else
            {
                    $availableTypes = [ 'image/png', 'image/jpg', 'image/jpeg' ];
                
                if( !in_array( $_FILES['file']['type'] , $availableTypes ) ) // si le type du fichier ne correspond pas
                {
                    $errorMessages['fileError'][] = 'Ce format de fichier n\'est pas autorisé.';
                }
                
                if( $_FILES['file']['size'] > 2097152 ) // si superieur à 2Mo
                {
                    $errorMessages['fileError'][] = 'Le fichier est trop gros, taille maximum 2Mo.';
                }
                
                $tmpNameFile = $_FILES['file']['tmp_name']; //Nom temporaire du fichier
                $nameFile = $_FILES['file']['name']; //Nom du fichier upload
                //-->le '.' va dire que pour chaque . tu créer une donnée dans le [] explode
                $explName = explode( '.', $nameFile ); // si fichier "bidul.jpeg" explode retourne ['bidul', 'jpeg']
                $finalName = $_POST['city'] . '.' . end( $explName ); // paris.jpeg
                
                $picturePath = 'public/img/destinations/' . $finalName;
            }
        }

            if( strlen( $_POST['caption'] ) > 120 )
            {
                $errorMessages['captionError'][] = 'La description ne doit pas dépasser 120 caractères.';
            }
 
                $statusList = ['Normal', 'Nouveauté', 'Prochainement', 'Bientôt retirée', 'Top 1 départ', 'Top 1 arrivée', 'Indisponible'];
            if( !in_array( $_POST['status'], $statusList ) ) //vérifie si le POST est différent du tableau $statusList
            {
                $errorMessages['statusError'][] = 'Le statut ne correspond pas à la sélection.';
            }
            
            if( isset( $_POST['is_available'] ) )
            {
                $isAvailable = 1;
            }
            else
            {
                $isAvailable = 0;
            }
            
            if( $errorMessages === [] )
            {
                if ( file_exists( $finalName ) )
                {
                    unlink("public/img/destinations");
                }
                move_uploaded_file( $tmpNameFile, $picturePath ); // enregistre l'image dans le chemin contenu dans $picturePath
                
                $pictureModel = new Picture();
                
                $pictureModel->addPictures([
                    $_POST['city'],
                    $picturePath,
                    $_POST['caption']
                ]);
                
                $lastPicture = $pictureModel->getLastPicture();
                
                $destinationModel->addDestinations([
                    $_POST['country'],
                    $_POST['city'],
                    $_POST['world_area'],
                    $_POST['description'],
                    $price,
                    $lastPicture['id_picture'],
                    $_POST['status'],
                    $isAvailable
                ]);
                
                Request::redirect('pannelDestinations');
            }
            else
            {
                $_SESSION['formDestinationsErrors'] = $errorMessages;
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }
    }
    
    public function displayFormEditDestinations()
    {
        $destinationModel = new Destination();
        $destination = $destinationModel->getDestinationById(
            $_GET['destinationid']
            );
        $pictureModel = new Picture();
        $picture = $pictureModel->getPictureById(
            $_GET['pictureid']
            );
        $view = "formEditDestinations.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function submitEditDestinations()
    {
        $destinationModel = new Destination();
        $pictureModel = new Picture();
 
        if( Request::hasValue( $_POST['country'] ) &&
            Request::hasValue( $_POST['city'] ) &&
            Request::hasValue( $_POST['world_area'] ) &&
            Request::hasValue( $_POST['description'] ) &&
            Request::hasValue( $_POST['price'] ) &&
            Request::hasValue( $_POST['caption'] ) &&
            Request::hasValue( $_POST['status'] )
            )
        {
            $errorMessages = [];
                
            if( strlen( $_POST['country'] ) > 50 )
            {
                $errorMessages['countryError'][] = 'Le pays ne doit pas dépasser 50 caractères.';
            }

            if( strlen( $_POST['city'] ) > 60 )
            {
                $errorMessages['cityError'][] = 'La ville ne doit pas dépasser 60 caractères.';
            }

            if( strlen( $_POST['world_area'] ) > 20 )
            {
                $errorMessages['world_areaError'][] = 'Le continent ne doit pas dépasser 20 caractères.';
            }

                $worldAreaList = ['Europe', 'Asie', 'Amérique du Nord', 'Amérique du Sud', 'Afrique', 'Océanie', 'Antarctique', 'Inconnu' ];
            
            if( !in_array( $_POST['world_area'], $worldAreaList ) )
            {
                $errorMessages['world_areaError'][] = 'Le continent ne correspond pas à la sélection.';
            }
            
            if( !is_numeric( $_POST['price'] ) )
            {
                $errorMessages['priceError'][] = 'Le prix ne doit contenir que des chiffres.';
            }
                
            if( $_POST['price'] > 9999999 )
            {
                $errorMessages['priceError'][] = 'Le prix ne doit pas dépasser 1M (1000000).';
            }

            if( isset( $_FILES['file']) && $_FILES['file']['error'] != 0 )
                {
                //-->erreur liée à FILE_UPLOAD_ERROR:
                switch( $_FILES['file']['error'] )
                {
                    case 1:
                        $errorMessages['fileError'][] = 'La taille du fichier dépasse la limite autorisée par le serveur (max: 2Mo).'; //-->case 1 lié à php.ini
                        break;
                    case 3:
                        $errorMessages['fileError'][] = 'Le fichier téléchargé n\'a été que partiellement téléchargé.';
                        break;
                    case 4:
                        $picturePath = $_POST['current_file'];
                        break;
                    case 6:
                        $errorMessages['fileError'][] = 'Il y a un soucis de notre côté, veuillez réesayer plus tard.';
                        break;
                    case 7:
                        $errorMessages['fileError'][] = 'Il y a un soucis de notre côté, veuillez réesayer plus tard.';
                        break;
                    case 8:
                        $errorMessages['fileError'][] = 'Erreur inconnue';
                        break;
                }
            }
            else
            {
                    $availableTypes = [ 'image/png', 'image/jpg', 'image/jpeg' ];
                
                if( !in_array( $_FILES['file']['type'] , $availableTypes ) ) // si le type du fichier ne correspond pas
                {
                    $errorMessages['fileError'][] = 'Ce format de fichier n\'est pas autorisé.';
                }
                
                if( $_FILES['file']['size'] > 2097152 ) // si superieur à 2Mo
                {
                    $errorMessages['fileError'][] = 'Le fichier est trop gros, taille maximum 2Mo.';
                }
                
                $tmpNameFile = $_FILES['file']['tmp_name']; //Nom temporaire du fichier
                $nameFile = $_FILES['file']['name']; //Nom du fichier upload
                //-->le '.' va dire que pour chaque . tu créer une donnée dans le [] explode
                $explName = explode( '.', $nameFile ); // si fichier "bidul.jpeg" explode retourne ['bidul', 'jpeg']
                $finalName = $_POST['city'] . '.' . end($explName); // paris.jpeg
                    
                $picturePath = 'public/img/destinations/' . $finalName;
                    
                if (file_exists($finalName))
                {
                    unlink("public/img/destinations");
                }
                move_uploaded_file($tmpNameFile, $picturePath);
            }

            if( strlen( $_POST['caption']) > 120 )
            {
                 $errorMessages['captionError'][] = 'La description ne doit pas dépasser 120 caractères.';
            }

                $statusList = ['Normal', 'Nouveauté', 'Prochainement', 'Bientôt retirée', 'Top 1 départ', 'Top 1 arrivée', 'Indisponible'];

            if( !in_array( $_POST['status'], $statusList ) )
            {
                $errorMessages['statusError'][] = 'Le statut ne correspond pas à la sélection.';
            }

            if(isset( $_POST['is_available'] )) //checkbox
            {
                $isAvailable = 1;
            }
            else
            {
                $isAvailable = 0;
            }
                    
            if( $errorMessages === [] )
            {
                $pictureModel->editPicture([
                    $_POST['city'],
                    $picturePath,
                    $_POST['caption'],
                    $_GET['pictureid']
                ]);
                
                $destinationModel->editDestination([
                    $_POST['country'],
                    $_POST['city'],
                    $_POST['world_area'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['status'],
                    $isAvailable,
                    $_GET['destinationid']
                ]);
                    
                Request::redirect('pannelDestinations');
            }
        }
        else
        {
            $_SESSION['formDestinationsErrors'] = $errorMessages;
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }
    }
    
    public function displayFormDeleteDestinations()
    {
        $destinationModel = new Destination();
        $destinationValues = $destinationModel->getDestinationById(
            $_GET['destinationid']    
        );
        $pictureModel = new Picture();
        $pictureValues = $pictureModel->getPictureById(
            $_GET['pictureid']
        );
    
        $view = 'formDeleteDestinations.phtml';
        include_once 'views/layout.phtml';
    }
    
    public function submitDeleteDestinations()
    {
        $destinationModel = new Destination();
        $pictureModel = new Picture();
        
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
            $errorMessage['checkDeleteDestinationError'] = 'Veuillez cocher cette case';
        }
            
        if( $errorMessage === [] )
        {
        
            $destinationModel->deleteDestination(
                $_GET['destinationid']
            );
            $pictureModel->deletePicture(
                $_GET['pictureid']
            );
        
            Request::redirect('pannelDestinations');
        }
        else
        {
            $_SESSION['formDeleteDestinationError'] = $errorMessage;
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}