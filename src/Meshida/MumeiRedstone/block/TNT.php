<?php


namespace Meshida\MumeiRedstone\block;


use Meshida\MumeiRedstone\Loader;

class TNT extends \pocketmine\block\TNT implements RedstoneComponent
{

    public function onNearbyBlockChange(): void
    {
        if (Loader::getTileSource($this->getLevel())->isBlockIndirectlyGettingPowered($this->x, $this->y, $this->z)) {
            $this->ignite();
        }
    }
}