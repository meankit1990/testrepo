<?php
class ResponseModel {
	private $code;
	private $message;
        private $status;
        private $data;
	public function getCode(){
		return $this->code;
	}
	public function getMessage(){
		return $this->message;
	}
	public function setCode($id){
		$this->code = $id;
	}
	public function setMessage($message){
		$this->message = $message;
	}
        public function setStatus($status){
		$this->status = $status;
	}
        public function setData($code,$message,$status){
                $this->code = $code;
		$this->message = $message;
		$this->status = $status;
	}
        public function setResponseData($data){
            $this->data=$data;
        }
	public function toJSON(){
		$finalJson = get_object_vars($this);
		return $finalJson;
	}
}
?>