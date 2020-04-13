<?php
namespace Meshida\MumeiRedstone\block;

use Meshida\MumeiRedstone\TileSource;

interface RedstonePowerSource extends RedstoneComponent
{

    public function getDirectSignal(TileSource $region, int $x, int $y, int $z, int $side);

}