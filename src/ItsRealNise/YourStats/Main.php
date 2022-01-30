<?php

namespace ItsRealNise\YourStats;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\Listener;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\scheduler\ClosureTask;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\block\{BlockPlaceEvent, BlockBreakEvent};
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\event\player\{PlayerJumpEvent, PlayerDeathEvent};

use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {
    
    /** @var Config $config */
    public $config;
    
    /** @var array $breakAll */
    public $breakAll = [];
    
    /** @var array $placeAll */
    public $placeAll = [];
    
    /** @var array $jumpAll */
    public $jumpAll = [];
    
    /** @var array $deathAll */
    public $deathAll = [];
    
    /** @var array $killAll */
    public $killAll = [];
    
    public function onEnable(): void{
$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->break = new Config($this->getDataFolder() . "break.yml", Config::YAML);
        $this->breakAll = $this->break->getAll();
        $this->place = new Config($this->getDataFolder() . "place.yml", Config::YAML);
        $this->placeAll = $this->place->getAll();
        $this->jump = new Config($this->getDataFolder() . "jump.yml", Config::YAML);
        $this->jumpAll = $this->jump->getAll();
        $this->death = new Config($this->getDataFolder() . "death.yml", Config::YAML);
        $this->deathAll = $this->death->getAll();
        $this->kill = new Config($this->getDataFolder() . "kill.yml", Config::YAML);
        $this->killAll = $this->kill->getAll();
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function (): void {
                $this->break->setAll($this->breakAll);
                $this->place->setAll($this->placeAll);
                $this->jump->setAll($this->jumpAll);
                $this->death->setAll($this->deathAll);
                $this->kill->setAll($this->killAll);
                $this->break->save();
                $this->place->save();
                $this->jump->save();
                $this->death->save();
                $this->kill->save();
            }), 20);
    }
    
    public function onDisable() : void{
        $this->break->setAll($this->breakAll);
        $this->place->setAll($this->placeAll);
        $this->jump->setAll($this->jumpAll);
        $this->death->setAll($this->deathAll);
        $this->kill->setAll($this->killAll);
        $this->break->save();
        $this->place->save();
        $this->jump->save();
        $this->death->save();
        $this->kill->save();
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    switch($cmd->getName()){
      case "ystats":
        if($sender instanceof Player){
          $this->openYourStats($sender);
        }else{
          $sender->sendMessage($this->config->get("use-ingame"));
        }
      break;
    }
    return true;
  }
  
    public function onBreak(BlockBreakEvent $event) {
      $sender = $event->getPlayer();
      $this->addBreak($sender);
  }
  
  public function onPlace(BlockPlaceEvent $event) {
      $sender = $event->getPlayer();
      $this->addPlace($sender);
  }
  
  public function onJump(PlayerJumpEvent $event) {
      $sender = $event->getPlayer();
      $this->addJump($sender);
  }
  
  public function onEntityDamage(EntityDamageEvent $event): void
	{
		$victim = $event->getEntity();
		if (!$victim instanceof Player) {
			return;
		}
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if (!$damager instanceof Player) {
				return;
			}
			if ($event->getFinalDamage() > $victim->getHealth()) {
				$this->addKill($damager);
				$this->addDeath($victim);
			}
			return;
		}
		if ($event->getFinalDamage() > $victim->getHealth()) {
			$this->addDeath($victim);
		}
	}
  
    public function addBreak(Player $sender) {
      $name = strtolower($sender->getName());
      if(!isset($this->breakAll[$name])) {
          $this->breakAll[$name] = 0;
        } else {
            $this->breakAll[$name] += 1;
        }
    }
    
    public function addPlace(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->placeAll[$name])) {
            $this->placeAll[$name] = 0;
        } else {
            $this->placeAll[$name] += 1;
        }
    }
    
    public function addJump(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->jumpAll[$name])) {
            $this->jumpAll[$name] = 0;
        } else {
            $this->jumpAll[$name] += 1;
        }
    }
    
    public function addDeath(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->deathAll[$name])) {
            $this->deathAll[$name] = 0;
        } else {
            $this->deathAll[$name] += 1;
        }
    }
    
    public function addKill(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->killAll[$name])) {
            $this->killAll[$name] = 0;
        } else {
            $this->killAll[$name] += 1;
        }
    }
    
    public function getBreak(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->breakAll[$name])){
            return 0;
        } else {
            return $this->breakAll[$name];
        }
    }
    
    public function getPlace(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->placeAll[$name])){
            return 0;
        } else {
            return $this->placeAll[$name];
        }
    }
    
    public function getJump(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->jumpAll[$name])){
            return 0;
        } else {
            return $this->jumpAll[$name];
        }
    }
    
    public function getDeath(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->deathAll[$name])){
            return 0;
        } else {
            return $this->deathAll[$name];
        }
    }
    
    public function getKill(Player $sender) {
        $name = strtolower($sender->getName());
        if(!isset($this->killAll[$name])){
            return 0;
        } else {
            return $this->killAll[$name];
        }
    }
    
    public function openYourStats($sender){
        $form = new SimpleForm(function (Player $sender, int $data = null){
            if($data === null){
                return true;
            }
            switch($data){
                case 0:
                break;
                
             }
        });
        $name = $sender->getName();
        $eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        if($eco == null) {
            $this->getServer()->getPluginManager()->disablePlugins();
            $this->getServer()->getLogger()->alert("EconomyAPI not found");
        }
        $place = $this->getPlace($sender);
        $break = $this->getBreak($sender);
        $jump = $this->getJump($sender);
        $death = $this->getDeath($sender);
        $kill = $this->getKill($sender);
        $x = intval(round($sender->getPosition()->getX()));
        $y = intval(round($sender->getPosition()->getY()));
        $z = intval(round($sender->getPosition()->getZ()));
        $world = $sender->getWorld()->getProvider()->getWorldData()->getName();
        $first = date("F, j Y H:i:s", $sender->getFirstPlayed() / 1000);
        $mmk = str_replace(["{name}", "{first_played}", "{ping}", "{money}", "{x}", "{y}", "{z}", "{world}", "{place}", "{break}", "{death}", "{kill}", "{jump}"], [$sender->getName(), $first, $sender->getNetworkSession()->getPing(), $eco->myMoney($sender), $x, $y, $z, $world, $place, $break, $death, $kill, $jump], $this->config->get("stats"));
        $form->setTitle($this->config->get("ui-title"));
        $form->setContent($mmk);
        $form->addButton("§cExit",0, "textures/ui/cancel");
        $form->sendToPlayer($sender);
        return $form;
    }
}
