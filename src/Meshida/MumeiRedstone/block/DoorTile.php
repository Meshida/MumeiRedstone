<?php

namespace Meshida\MumeiRedstone\block;

use pocketmine\block\Door;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use Meshida\MumeiRedstone\Loader;

class DoorTile extends Door implements RedstoneComponent
{

    public function onRefleshRedstoneSignal($power): void
    {
        //$powered = Loader::getTileSource($this->getLevel())->isBlockIndirectlyGettingPowered($this->getX(), $this->getY(), $this->getZ());
        $powered = $power > 0 ? true : false;
        var_dump($powered);
        $this->setOpen($this->getLevel(), $this->getX(), $this->getY(), $this->getZ(), $powered, NULL);
        //echo "refD";
    }

    public function isOpen(): bool
    {
        //echo ($this->getDamage() & 0x04) > 0 ? "true" : "false";
        return ($this->getDamage() & 0x04) > 0 ? true : false;
    }

    public function setOpen(Level $level, $x, $y, $z, $open, ?Player $opener = null)
    {
        if (!$this->isOpen() && $open) {
            $this->onActivate();
        }
        if ($this->isOpen() && !$open) {
            $this->onActivate();
        }
    }
}
