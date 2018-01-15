<?php
include 'models/CarModel.php';
include 'models/UserModel.php';

class SearchRide {
	private $rideId;
	private $source;
	private $destination;
	private $rideTimeStamp;
	private $seatsOffered;
	private $ridePreferences;
	private $isReturntrip;
	private $returnTimeStamp;
	private $rideRemark;
	private $driverDetails;
	private $carDetails;
	private $routePoints;

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
	public function setRideId($rideId){
		$this->rideId = $rideId;
	}
	public function setSourceAddress($sourceAddress,$sourceLat,$sourceLong){
		$this->source =  [RIDE_SOURCE_ADDRESS => $sourceAddress,
			RIDE_SOURCE_LAT =>$sourceLat,
			RIDE_SOURCE_LONG=>$sourceLong	
		];
	}
	public function setDestinationAddress($destinationAddress,$destinationLat,$destinationLong){
		$this->destination =  [RIDE_DESTINATION_ADDRESS => $destinationAddress,
			RIDE_DESTINATION_LAT =>$destinationLat,
			RIDE_DESTINATION_LONG=>$destinationLong	
		];
	}
	public function setRideTimeStamp($value){
		$this->rideTimeStamp = $value;
	}
	public function setSeatsOffered($value){
		$this->seatsOffered = $value;
	}
	public function setRidePreferences($somking,$baggage,$animals,$food)
	{
		$this->ridePreferences = [
				RIDE_SMOKING_ALLOWED => $this->getBooleanValue($somking),
				RIDE_BAGGAGE_ALLOWED => $this->getBooleanValue($baggage),
				RIDE_ANIMAL_ALLOWED => $this->getBooleanValue($animals),
				RIDE_DRINK_ALLOWED => $this->getBooleanValue($food)
		];
	}
	public function setIsReturnTrip($value){
		$resultValue = $this->getBooleanValue($value);
		$this->isReturntrip = $resultValue;
	}
	public function setReturnTimeStamp($value){
		$this->returnTimeStamp = $value;
	}
	public function setRideRemarks($value){
		$this->rideRemark = $value;
	}
	
	public function setUserData(UserModel $userQueryResult){
		$user = $userQueryResult;
		$this->driverDetails = $user->toJSON();
	}
	public function getUserData(){
		return $this->driverDetails;
	}
	public function setCarData(CarModel $carQueryResult){
		$car = $carQueryResult;
		$this->carDetails = $car->toJSON();
	}
	
	public function setRouteData($routePoints)
	{
		$this->routePoints = $routePoints;
	}
	public function toJSON(){
    return get_object_vars($this);
	}
	
}

?>