<?php
namespace Trinket\Utils;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class ThreadedQueue extends \Threaded{

	private $queue;

	public function __construct() {
		$this->queue = serialize([]);
	}

	public function getNext() {
		$queue = unserialize($this->queue);
		$key = array_shift($queue);
		array_splice($queue, 0, 1);
		$this->queue = serialize($queue);
		return $key;
	}

	public function getQueue() {
		return unserialize($this->queue);
	}

	public function push($obj) {
		$queue = unserialize($this->queue);
		$queue[] = $obj;
		$this->queue = serialize($queue);
	}
}