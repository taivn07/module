<?php
use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\PresenceOf,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Users extends Model {
	public $username;

	public $email;

	public $password;

	public $accessToken;

	public $active;

	public $createdAt;

	public $modifiedAt;

	public function setAccessToken() {
		$this->accessToken = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(32)));
	}

	public function validation() {
		$this->validate(new Uniqueness(array(
			'field' => 'username',
			'message' => 'username is existed'
		)));

		$this->validate(new PresenceOf(array(
			'field' => 'username',
			'message' => 'username is required'
		)));

		$this->validate(new PresenceOf(array(
			'field' => 'password',
			'message' => 'password is required'
		)));

		$this->validate(new Uniqueness(array(
			'field' => 'email',
			'message' => 'email is existed'
		)));

		if ($this->validationHasFailed() == true) {
			return false;
		}
	}

	public function beforeValidationOnCreate() {
		$this->password = sha1($this->password);
		$this->active = 0;
		$this->accessToken = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(32)));
	}

	public function beforeCreate() {
		// set the creation date
		$this->createdAt = date('Y-m-d H:i:s');
		// set the mofification date
		$this->modifiedAt = date('Y-m-d H:i:s');
	}

	public function beforeUpdate() {
		// set the modification date
		$this->modifiedAt = date('Y-m-d H:i:s');
	}

	public function afterCreate() {
		$this->getDI()->getMail()->send(
			array(
				$this->email => $this->username
			),
			'You have registed new account!!',
			'confirmation',
			array(
				'confirmUrl' => 'paditech.com'
			)
		);
	}
}