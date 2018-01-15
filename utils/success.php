<?php
class Success {
	private $suceessId;
	private $messsage;
	public function getErrorId(){
		return $this->errorId;
	}
	public function getErrorMessage(){
		return $this->errorMsg;
	}
	public function setErrorId($id){
		$this->errorId = $id;
	}
	public function setErrorMessage($errorMSg){
		$this->errorMsg = $errorMSg;
	}
	public function toJSON(){
		$finalJson = json_encode(get_object_vars($this));
		return $finalJson;
	}
}
?>