<?php
class TestController extends ControllerBase {
	
	public function loginAction() {
		$result = array(
			'result' => 'OK',
			'User' => array(
				'UserId' => 7,
				'UserName' => 'admin',
				'Code' => 01,
				'IsAdmin' => true,
				'IsAdmin1' => true
			)
		);

		// set response to json
        $this->setJsonResponse();

		return $result;
	}
}
