<?php
namespace Trinket\Utils;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class Queue{

	private $queue;

	public function __construct() {
		$this->queue = [];
	}

	public function getNext() {
		$key = array_shift($this->queue);
		array_splice($this->queue, 0, 1);
		return $key;
	}

	public function getQueue() {
		return $this->queue;
	}

	public function push($obj) {
		$this->queue[] = $obj;
	}
}