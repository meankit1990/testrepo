<?php
class UserModel {
    public $user_id; //String
    public $first_name;
    public $last_name;
    public $user_email;
    public $user_type;
    public $lifetime_premium;
    public $expiry_time;
    
    public function getUser_id() { 
         return $this->user_id;
    }
    public function setUser_id($user_id) { 
         $this->user_id = $user_id;
    }
    public function getUserFirstName(){
        return $this->first_name;  
    }
    public function setUserFirstName($firstName){
        $this->first_name=$firstName;
    }
    public function getUserLastName(){
        return $this->last_name;
    }
    public function setUserLastName($lastName){
        $this->last_name=$lastName;
    }
    public function getUserEmail(){
        return $this->user_email;
    }
    public function setUserEmail($email){
        $this->user_email = $email;
    }
    public function getUserType() { 
         return $this->user_type; 
    }
    public function setUserType($user_type) { 
         $this->user_type = $user_type; 
    }
    public function getLifeTimePremium() { 
         return $this->lifetime_premium; 
    }
    public function setLifeTimePremium($lifetime_premium) { 
         $this->lifetime_premium = $lifetime_premium; 
    }
    public function getExpiryTime() { 
         return $this->expiry_time; 
    }
    public function setExpiryTime($expiry) { 
         $this->expiry_time = $expiry; 
    }
}
?>