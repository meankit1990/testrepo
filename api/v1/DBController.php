<?php
require_once '../../config/dbCon.php';
require_once '../../config/ResponseModel.php';
require_once '../../config/BaseApiResponse.php';
require_once '../../config/ErrorConstants.php';
require_once '../../config/Constants.php';
require_once '../../models/UserModel.php';

class DBController {
    private static $REGISTER_USER = "registerUser";
    private static $LOGIN_USER="loginUser";
    private $params;
    private $response;
    private $con;
    private $baseReponse;
    public function __construct(){
        // Instantiate connection class
        $pdo = new dbCon();
        // Assign the connection to $con (or whatever variable you like)
        $this->con = $pdo->connection();
        $this->response = new ResponseModel();
        $this->baseReponse = new BaseApiResponse();
        
    }
    public function serveRequest($operation){
       switch($_SERVER['REQUEST_METHOD']){   
        case 'GET':     
            $this->params = json_encode($_GET);
            $this->params = json_decode($this->params);
            break;
        case 'POST':
            $this->params = json_decode ( file_get_contents ( "php://input" ) );
            break;
       }
       
       switch($operation){
           case self::$REGISTER_USER:{
               $this->registerUser($this->params);
               break;
           }
           case self::$LOGIN_USER:{
               $this->loginUser($this->params);
               break;
           }
           default:{
               $this->response->setData(404,METHOD_NOT_FOUND,false);
               $this->baseReponse->sendResponse($this->response->toJSON());  
           }
       }
    }
    private function registerUser($params){
        $email = strip_tags($params->email);
        $password = password_hash($params->password,CRYPT_SHA512);
        $query = "INSERT INTO ".TABLE_USER."(".COL_USER_ID.",".COL_USER_PASSWORD.") VALUES ('$email','$password')";
        try{
        $this->con->beginTransaction();
        $statement = $this->con->prepare($query);
        if($statement->execute()){
            $this->response->setData(200,"User registered successfully",true);
            $userId = $this->con->lastInsertId(); 
            $this->response->setResponseData($this->getUserDetails($userId));
            $this->con->commit();   
            
        }else{
            throw new Exception("Something went wrong", 500);
        }  
        }catch(Exception $ex){
            switch($ex->getCode()){
                case 23000:
                     $this->response->setData($ex->getCode(),"User with this email already exist",false);
                     break;
                default:
                     $this->response->setData($ex->getCode(),$ex->getMessage(),false);
            }
           $this->con->rollback();
        }
        finally{$this->baseReponse->sendResponse($this->response->toJSON());  
        }
    }
    private function loginUser($params){
        $username = strip_tags($params->username);
        $password =md5($params->password);
        try{
        $sql = "SELECT * FROM ".TABLE_USER." WHERE ".COL_USER_ID."='$username'";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if($password == $row[COL_USER_PASSWORD]){
                $this->response->setData(200,"User logged in successfully",true);
             
                $this->response->setResponseData($this->getUserDetails($row[COL_ID]));
            }else{
                $this->response->setData(401,"User not authorized",false);
            }
        }
        }catch(Exception $ex){
            $this->response->setData($ex->getCode(),$ex->getMessage(),false);
        }finally{
            $this->baseReponse->sendResponse($this->response->toJSON());  
        }
    }
    private function getUserDetails($userId){
        $sql = "SELECT * from ".TABLE_USER." WHERE ".COL_ID."=".$userId;
        foreach ($this->con->query($sql) as $row) {
                $userModel = new UserModel();
                $userModel->setUser_id($row[COL_ID]);
                $userModel->setUserFirstName($row[COL_FIRST_NAME]);
                $userModel->setUserLastName($row[COL_LAST_NAME]);
                $userModel->setUserEmail($row[COL_EMAIL]);
                if($row[COL_PREMIUM]){
                    $userModel->setUserType(COL_PREMIUM);
                }else if($row[COL_PREMIUM_PLUS]){
                    $userModel->setUserType(COL_PREMIUM_PLUS);
                }else if($row[COL_VIP]){
                    $userModel->setUserType(COL_VIP);
                }
                $userModel->setExpiryTime($row[COL_EXPIRY_TIME]);
                $userModel->setLifeTimePremium($row[COL_LIFE_TIME]);
        }
        return $userModel;
    }
    private function updateUserProfile($params){
        
    }
}
