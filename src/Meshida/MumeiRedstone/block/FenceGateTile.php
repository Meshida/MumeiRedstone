<?php

namespace Meshida\MumeiRedstone\block;

use pocketmine\block\FenceGate;
use pocketmine\level\sound\DoorSound;
use Meshida\MumeiRedstone\Loader;

class FenceGateTile extends FenceGate implements RedstoneComponent, RedstoneStuff
{

    public function onNearbyBlockChange(): void
    {
        parent::onNearbyBlockChange();
        /*
        $data = $this->getDamage();
        $powered = Loader::getTileSource($this->getLevel())->isBlockIndirectlyGettingPowered($this->getX(), $this->getY(), $this->getZ());

        if ($powered && !$this->isOpen($data)) {
            $this->getLevel()->setBlockDataAt($this->getX(), $this->getY(), $this->getZ(), $data | 4);
            $this->getLevel()->addSound(new DoorSound($this->add(0.5, 0.5, 0.5)));
        }
        if (!$powered && $this->isOpen($data)) {
            $this->getLevel()->setBlockDataAt($this->getX(), $this->getY(), $this->getZ(), $data & -5);
            $this->getLevel()->addSound(new DoorSound($this->add(0.5, 0.5, 0.5)));
        }
        */
    }

    public function onRefleshRedstoneSignal($power): void
    {
        $data = $this->getDamage();
        //$powered = Loader::getTileSource($this->getLevel())->isBlockIndirectlyGettingPowered($this->getX(), $this->getY(), $this->getZ());
        $powered = $power > 0 ? true : false;
        var_dump($power);

        if (!$powered && $this->isOpen()) {
            $this->onActivate();
            //$this->getLevel()->setBlockDataAt($this->getX(), $this->getY(), $this->getZ(), $data - 4);
            //$this->getLevel()->addSound(new DoorSound($this->add(0.5, 0.5, 0.5)));
            echo "c";
        }
        if ($powered && !$this->isOpen()) {
            $this->onActivate();
            //$this->getLevel()->setBlockDataAt($this->getX(), $this->getY(), $this->getZ(), $data + 4);
            //$this->getLevel()->addSound(new DoorSound($this->add(0.5, 0.5, 0.5)));
            echo "o";
        }
        //echo "ref";
    }

    public function isOpen(): bool
    {
        return ($this->getDamage() & 0x04) > 0 ? true : false;
    }
}
