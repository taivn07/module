<?php
class TestController extends ControllerBase {
	
	public function loginAction() {
		$result = array(
			'result' => 'OK',
			'User' => array(
				array(
					'UserId' => 7,
					'UserName' => 'admin',
					'Code' => 01,
					'IsAdmin' => true,
					'IsAdmin1' => true
				)
			)
		);

		// set response to json
        $this->setJsonResponse();

		return $result;
	}

	public function menuAction() {
		$result = array(
			'result' => 'OK',
			'Tree' => array(
				array(
					'id' => '01',
					'parentid' => '',
					'label' =>  'TỔNG CÔNG TY ĐIỆN LỰC MIỀN TRUNG [ 10 ]'
				),
				array(
					'id' => '0101',
					'parentid' => '01',
					'label' =>  'CÔNG TY ĐIỆN LỰC QUẢNG BÌNH [ 3 ]'
				),
				array(
					'id' => '0102',
					'parentid' => '01',
					'label' =>  'CÔNG TY ĐIỆN LỰC PHỤ [ 0 ] '
				),
				array(
					'id' => '0104',
					'parentid' => '01',
					'label' =>  'CÔNG TY ĐIỆN LỰC QUẢNG NAM [ 0 ] '
				),
				array(
					'id' => '0105',
					'parentid' => '01',
					'label' =>  'CÔNG TY ĐIỆN LỰC QUẢNG NGÃI [ 0 ] '
				),
				array(
					'id' => '0106',
					'parentid' => '01',
					'label' =>  'CÔNG TY ĐIỆN LỰC BÌNH ĐỊNH [ 2 ]'
				),
				array(
					'id' => '010601',
					'parentid' => '0106',
					'label' =>  'Điểm đo số 8 [ 2 ]',
					'listcongto' => array(
						array(
							'id' => '010601-12',
							'parentid' => '010601',
							'label' => '12345'
						),
						array(
							'id' => '010601-12',
							'parentid' => '010601',
							'label' => '12345'
						)
					)
				),
				array(
					'id' => '010601',
					'parentid' => '0106',
					'label' =>  'Điểm đo số 8 [ 2 ]',
				),
				array(
					'id' => '010101',
					'parentid' => '0101',
					'label' =>  'Điện lưc Quảng bình 1 [ 3 ]',
				),
				array(
					'id' => '01010101',
					'parentid' => '010101',
					'label' =>  'Điện lưc quận 1 [ 3 ]',
				),
				array(
					'id' => '0101010101',
					'parentid' => '01010101',
					'label' =>  'Điện lưc quận 1 [ 3 ]',
				),
				array(
					'id' => '010101010101',
					'parentid' => '0101010101',
					'label' =>  'Cụm dân cư 1 [ 3 ]',
				),
				array(
					'id' => '01010101010101',
					'parentid' => '010101010101',
					'label' =>  'Điểm đo 1 [ 3 ]',
					'listcongto' => array(
						array(
							'id' => '01010101010101-16',
							'parentid' => '01010101010101',
							'label' => '123456789'
						),
						array(
							'id' => '01010101010101-17',
							'parentid' => '01010101010101',
							'label' => '123456789'
						),
						array(
							'id' => '01010101010101-18',
							'parentid' => '01010101010101',
							'label' => '123456789'
						),
					)
				),
			)
		);
		
		// set response to json
        $this->setJsonResponse();
        
		return $result;
	}
}
