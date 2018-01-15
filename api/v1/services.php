<?php
require_once 'dbController.php';
$dbController = new DBController();
if(isset($_GET['operation']) && $_GET['operation']!="")
            $operation = $_GET['operation'];
         else{
            $response = new ResponseModel();
            $baseReponse = new BaseApiResponse();
            $response->setData(404,METHOD_NOT_FOUND,false);
            $baseReponse->sendResponse($response->toJSON());  
            exit();
         }
             
$dbController->serveRequest($operation);
?>
