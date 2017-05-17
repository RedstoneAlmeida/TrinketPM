<?php
namespace Trinket\Commands;

use Trinket\Trinket;
use Trinket\Network\Protocol\Packet;

use pocketmine\command\CommandSender;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class PacketCommand extends BaseCommand{

    private $plugin;

    public function __construct(Trinket $plugin){
        parent::__construct("packet", $plugin);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) 
    {
        if($sender instanceof Player)
        {
            $sender->sendMessage("Unable to run command as player.");
            return false;
        }

        $this->plugin->getSendQueue()->push((new Packet()));
    }
}