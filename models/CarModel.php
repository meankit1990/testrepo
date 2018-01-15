<?php
class CarModel {
	private $carId;
	private $licensePlateNumber;
	private $company;
	private $carModel;
	private $carColor;
	
	public function getcarId()
	{
		return $this->carId;
	}
	public function getlicensePlateNumber()
	{
		return $this->licensePlateNumber;
	}
	public function setCarId($carId)
	{
		$this->carId = $carId;
	}
	public function setLicensePlateNumber($licensePlateNumber)
	{
		$this->licensePlateNumber = $licensePlateNumber;
	}
	public function setCompany($value)
	{
		$this->company = $value;
	}
	public function setCarModel($value)
	{
		$this->carModel = $value;
	}
	public function setCarColor($value)
	{
		$this->carColor = $value;
	}
	
	public function toJSON(){
		return get_object_vars($this);
	}
}
?>