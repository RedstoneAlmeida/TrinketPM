<?php
namespace Trinket\Commands;

use pocketmine\Server;
use Trinket\Trinket;
use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

use pocketmine\command\CommandSender;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class TrinketCommand extends BaseCommand{

    private $plugin;

    public function __construct(Trinket $plugin) {
        parent::__construct("trinket", $plugin);
        $this->setDescription("View basic trinket information");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) {
        $data = $this->plugin->getThreadedStorage()->getAll();
        $sender->sendMessage("Latest Trinket Info\nVersion " . $this->plugin->getDescription()->getVersion() . "\nProtocol " . Info::PROTOCOL . "\nNetwork Players " . $data["players"]);
    }
}
