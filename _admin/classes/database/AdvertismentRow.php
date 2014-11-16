<?php

class AdvertismentRow extends KASDatabaseRow {
	protected $table = "advertisments";

	protected $defaults = array(
		'id' => 0,
		'location' => "",
		'status' => false,
		'content' => ""
	);

	public function validate() {
		$validator = $this->validator;

		// Validate ID
		$validator->digits('The ID must be a number greater than 0.')->validate('id');

		// Validate Location
		$validator->regex("/\A[a-zA-Z0-9\_\-]+\z/i", 'The given location is invalid.')->validate('location');

		// Validate Status
		$validator->oneOf(array(true, false, 1, 0, '1', '0'), 'The given status is not valid.')->validate('status');

		// Validate Content
		$validator->required()->validate('content');

		if ($validator->hasErrors()) {
			throw new Validator_Exception('Fields not valid!.', $validator->getAllErrors());
			return false;
		}

		return true;
	}
}

?>