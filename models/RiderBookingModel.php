<?php
include 'models/CarModel.php';
class RiderBookingModel {
	private $rideTimeStamp;
	private $rideId;
	private $requestId;
	private $source;
	private $destination;
	private $driverDetails;
	private $seatsOffered;
	private $ridePreferences;
	private $isReturntrip;
	private $returnTimeStamp;
	private $rideRemark;
	private $carDetails;
	private $routePoints;
	private $rideStatus;
	private $seatsRequested;
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
	public function setDriverDetails($driverDetails) {
	$driverDetail = $driverDetails;
		$this->driverDetails = $driverDetail->toJSON ();	
	}
	public function setRideId($rideId) {
		$this->rideId = $rideId;
	}
	public function setRideTimeStamp($rideTime) {
		$this->rideTimeStamp = $rideTime;
	}
	public function setRequestId($requestId) {
		$this->requestId = $requestId;
	}
	public function setSource($source) {
		$this->source = $source;
	}
	public function setDestination($destination) {
		$this->destination = $destination;
	}
	public function setSeatsOffered($value) {
		$this->seatsOffered = $value;
	}
	public function setRidePreferences($somking, $baggage, $animals, $food) {
		$this->ridePreferences = [ 
				RIDE_SMOKING_ALLOWED => $this->getBooleanValue ( $somking ),
				RIDE_BAGGAGE_ALLOWED => $this->getBooleanValue ( $baggage ),
				RIDE_ANIMAL_ALLOWED => $this->getBooleanValue ( $animals ),
				RIDE_DRINK_ALLOWED => $this->getBooleanValue ( $food ) 
		];
	}
	public function setIsReturnTrip($value) {
		$resultValue = $this->getBooleanValue ( $value );
		$this->isReturntrip = $resultValue;
	}
	public function setReturnTimeStamp($value) {
		$this->returnTimeStamp = $value;
	}
	public function setRideRemarks($value) {
		$this->rideRemark = $value;
	}
	public function setCarData(CarModel $carQueryResult) {
		$car = $carQueryResult;
		$this->carDetails = $car->toJSON ();
	}
	public function setRouteData($routePoints) {
		$this->routePoints = $routePoints;
	}
	public function  setRideStatus($rideStatus){
		$this->rideStatus = $rideStatus;
	}
	public function setSeatsRequested($seatsRequested){
		$this->seatsRequested = $seatsRequested;
	}
	public function toJSON() {
		return get_object_vars ( $this );
	}
	
}
?>