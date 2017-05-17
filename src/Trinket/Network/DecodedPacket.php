<?php
namespace Trinket\Network;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class DecodedPacket{
  
  public $identifier, $array;

  public function __construct($data)
  {
    if(!is_array($data))
    {
      $data = json_decode(trim($data), True);
    }
    $this->identifier = isset($data["id"]) ? $data["id"] : 0;
    $this->array = $data;
  }

  public function getId()
  {
    return $this->identifier;
  }

  public function get($index)
  {
    return isset($this->array[$index]) ? $this->array[$index] : null;
  }
  
  public function getAll()
  {
    return $this->array;
  }
}