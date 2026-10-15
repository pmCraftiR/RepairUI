<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\RepairUI\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use fernanACM\RepairUI\RP;
use fernanACM\RepairUI\utils\PluginUtils;
use fernanACM\RepairUI\language\Language;
use fernanACM\RepairUI\language\LangKey;
use fernanACM\RepairUI\permissions\Perms;
use fernanACM\RepairUI\manager\RepairManager;

class RepairCommand extends Command implements PluginOwned{

    public function __construct(private RP $plugin){
        parent::__construct(
            "repairui",
            "Open RepairUI by fernanACM",
            "/repairui [all|hand|lore|rename|help]",
            ["rp", "repair", "fix", "sipe"]
        );
        $this->setPermission(Perms::CMD);
    }

    public function getOwningPlugin(): Plugin{
        return $this->plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player){
            $sender->sendMessage("Use this command in-game");
            return;
        }

        if(!isset($args[0])){
            if(!$sender->hasPermission(Perms::CMD)){
                $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                return;
            }
            $this->plugin->getFormManager()->getMenu()->open($sender);
            PluginUtils::PlaySound($sender, "random.pop2", 1, 1);
            return;
        }

        switch(strtolower($args[0])){
            case "help":
            case "?":
                if(!$sender->hasPermission(Perms::HELP)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                $sender->sendMessage("§l§e»RepairUI«");
                $sender->sendMessage("§7» /repairui - Open RepairUI");
                $sender->sendMessage("§7» /repairui help - Command list");
                $sender->sendMessage("§7» /repairui all - Repair all items");
                $sender->sendMessage("§7» /repairui hand - Repair the items in your hand");
                $sender->sendMessage("§7» /repairui rename <name> - Rename the item in your hand");
                $sender->sendMessage("§7» /repairui lore <lore> - Lore the item in your hand");
                PluginUtils::PlaySound($sender, "random.pop2", 1, 1);
                break;

            case "all":
                if(!boolval($this->plugin->config->getNested("Settings.Commands.all", true))){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    return;
                }
                if(!$sender->hasPermission(Perms::REPAIR_ALL)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                RepairManager::getInstance()->sendInventoryAllRepaired($sender);
                break;

            case "hand":
                if(!boolval($this->plugin->config->getNested("Settings.Commands.hand", true))){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    return;
                }
                if(!$sender->hasPermission(Perms::REPAIR_HAND)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                RepairManager::getInstance()->sendRepairedItem($sender);
                break;

            case "lore":
                if(!boolval($this->plugin->config->getNested("Settings.Commands.lore", true))){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    return;
                }
                if(!$sender->hasPermission(Perms::LORE_CMD)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                if(!isset($args[1])){
                    $sender->sendMessage(RP::getPrefix() . "§cUse: /repairui lore <lore>");
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                $text = implode(" ", array_slice($args, 1));
                RepairManager::getInstance()->sendRenamedItem($sender, null, RepairManager::LORE_MODE, $text);
                break;

            case "rename":
                if(!boolval($this->plugin->config->getNested("Settings.Commands.rename", true))){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    return;
                }
                if(!$sender->hasPermission(Perms::RENAME_CMD)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                if(!isset($args[1])){
                    $sender->sendMessage(RP::getPrefix() . "§cUse: /repairui rename <name>");
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                $text = implode(" ", array_slice($args, 1));
                RepairManager::getInstance()->sendRenamedItem($sender, null, RepairManager::RENAME_MODE, $text);
                break;

            default:
                if(!$sender->hasPermission(Perms::CMD)){
                    $sender->sendMessage(RP::getPrefix() . Language::getMessage(LangKey::ERROR_NO_PERMISSION));
                    PluginUtils::PlaySound($sender, "mob.villager.no", 1, 1);
                    return;
                }
                $this->plugin->getFormManager()->getMenu()->open($sender);
                PluginUtils::PlaySound($sender, "random.pop2", 1, 1);
                break;
        }
    }
}