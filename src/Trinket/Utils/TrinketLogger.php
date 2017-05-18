<?php
namespace Trinket\Utils;

use pocketmine\utils\TextFormat;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class TrinketLogger{

  const ERROR = 0; const CRITICAL = 0;
  const DEBUG = 1;
  const INFO = 2;
  const WARNING = 3;

  protected function send($message, $level, $prefix, $color) {
    $now = time();
    $threadName = "TrinketThread";
    $message = TextFormat::toANSI(TextFormat::AQUA . "[" . date("H:i:s", $now) . "] " . TextFormat::RESET . $color . "[" . $threadName . "/" . $prefix . "]:" . " " . $message . TextFormat::RESET);

    echo($message . PHP_EOL);
  }

  public function critical($message) {
    $this->send($message, TrinketLogger::CRITICAL, "CRITICAL", TextFormat::RED);
  }

  public function error($message) {
    $this->send($message, TrinketLogger::ERROR, "ERROR", TextFormat::DARK_RED);
  }

  public function warning($message) {
    $this->send($message, TrinketLogger::WARNING, "WARNING", TextFormat::YELLOW);
  }

  public function info($message) {
    $this->send($message, TrinketLogger::INFO, "INFO", TextFormat::WHITE);
  }

  public function debug($message) {
    $this->send($message, TrinketLogger::DEBUG, "DEBUG", TextFormat::GRAY);
  }
}