<?php
class NearByRide {
	private $source;
	private $destination;
	private $totalCount;
	private function getBooleanValue($value){
		$resultValue;
		switch ($value){
			case 0:
				$resultValue= false;
				break;
			case 1:
				$resultValue =true;
				break;
			default:$resultValue = null;
				
		}
		return $resultValue;
	}

	public function setSourceAddress($sourceAddress){
		$this->source =  $sourceAddress;
	}
	public function setDestinationAddress($destinationAddress){
		$this->destination =   $destinationAddress;
	}
	public function setTotalCount($totalRides){
		$this->totalCount =   $totalRides;
	}
	public function toJSON(){
    return get_object_vars($this);
	}
	
}

?>