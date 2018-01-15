<?php

function sortUserAsc($a, $b) {
	if ($a->driverDetails->driverRating < $b->driverDetails->driverRating) {
		return - 1;
	} else if ($a->driverDetails->driverRating > $b->driverDetails->driverRating) {
		return 1;
	} else {
		return 0;
	}
}

function sortUserDesc($a, $b) {
	if ($a->driverDetails->driverRating > $b->driverDetails->driverRating) {
		return - 1;
	} else if ($a->driverDetails->driverRating < $b->driverDetails->driverRating) {
		return 1;
	} else {
		return 0;
	}
}

function sortTimeAsc($a, $b) {
	if ($a->rideTimeStamp < $b->rideTimeStamp) {
		return - 1;
	} else if ($a->rideTimeStamp > $b->rideTimeStamp) {
		return 1;
	} else {
		return 0;
	}
}

function sortTimeDesc($a, $b) {
	if ($a->rideTimeStamp > $b->rideTimeStamp) {
		return - 1;
	} else if ($a->rideTimeStamp < $b->rideTimeStamp) {
		return 1;
	} else {
		return 0;
	}
}
function sortDefault($a, $b) {
	if ($a->rideId < $b->rideId) {
		return - 1;
	} else if ($a->rideId > $b->rideId) {
		return 1;
	} else {
		return 0;
	}
}
?>