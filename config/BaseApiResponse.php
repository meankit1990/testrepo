<?php
class BaseApiResponse {
	private $response;
	public function setData($data) {
		$this->response = $data;
	}
	public function toJson() {
		return json_encode ( get_object_vars ( $this ) );
	}
        public function sendResponse($data){
            $this->setData($data);
            echo $this->toJson();
        }
    }
?>