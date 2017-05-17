<?php
namespace Trinket\Utils;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class Queue{

	private $queue;

	public function __construct()
	{
		$this->queue = [];
	}

	public function getNext()
	{
		return array_shift($this->queue);
	}

	public function getQueue()
	{
		return $this->queue;
	}

	public function addItem($item)
	{
		array_push($this->queue, $item);
	}
}