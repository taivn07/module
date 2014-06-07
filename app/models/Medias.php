<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\PresenceOf,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Medias extends Model
{
	public $id;

	public $extension;

	public $size;

	public $originPath;

	public $thumbnailPath;

	public $thumbnailSize;

	public $createdAt;

	public $modifiedAt;

	public function getFullPath() {
		return BASE_URL.$this->originPath;
	}

	public function getFullThumbnailPath() {
		return BASE_URL.$this->thumbnailPath;
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

	// validation
	public function validation() {
		$this->validate(new PresenceOf(array(
			'field' => 'extension',
			'message' => 'The extension is required'
		)));

		if ($this->validationHasFailed() == true) {
			return false;
		}
	}
}