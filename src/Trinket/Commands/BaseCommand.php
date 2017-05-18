<?php
namespace Trinket\Commands;

use Trinket\Trinket;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

abstract class BaseCommand extends Command implements PluginIdentifiableCommand{

    private $plugin;

    public function __construct(String $name, Trinket $plugin) {
        parent::__construct($name);
        $this->plugin = $plugin;
        $this->usageMessage = "";
    }

    public function getPlugin() {
        return $this->plugin;
    }
}