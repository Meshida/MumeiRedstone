<?php
namespace Meshida\MumeiRedstone\block;

interface RedstoneComponent
{

    public function onRefleshRedstoneSignal(Block $source, Block $from, int $power): void;

}