<?php
namespace Trinket\Utils;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class ThreadedStorage extends \Threaded{

	private $data;

	public function __construct() {
		$this->data = serialize([]);
	}

	public function setData($data) {
		return $this->data = serialize($data);
	}

	public function getAll() {
		return unserialize($this->data);
	}
}