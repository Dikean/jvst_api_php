<?php
	function addTime($originalDate, $timeToAdd)
	{
		// Create a DateTime object with the original date
		$dateTime = new DateTime($originalDate);

		// Add 6 months to the DateTime object
		$dateTime->add(new DateInterval($timeToAdd));

		// Get the result as a string in the desired format (change this to your desired format)
		$resultDate = $dateTime->format('Y-m-d');

		return $resultDate;
	}
?>