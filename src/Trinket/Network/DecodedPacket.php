<?php
namespace Trinket\Network;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class DecodedPacket{
  
  public $id;

  public function __construct($data) {
    if(is_string($data)) {
      $data = json_decode(trim($data), True);
    }
    $this->id = isset($data["id"]) ? $data["id"] : 0;

    if(!$data) {
      $this->protocol = 0;
      return;
    }

    foreach($data as $key => $element) {
      $this->{$key} = $element;
    }
  }

  public function getId() {
    return $this->id;
  }
}