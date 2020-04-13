<?php

namespace Meshida\MumeiRedstone\block;

use pocketmine\block\Block;
use Meshida\MumeiRedstone\TileSource;

class RedstoneBlockTile extends Block implements RedstonePowerSource
{

    public function isSignalSource(): bool
    {
        return true;
    }

    public function getDirectSignal(TileSource $region, int $x, int $y, int $z, int $data): int
    {
        return 15;
    }
}