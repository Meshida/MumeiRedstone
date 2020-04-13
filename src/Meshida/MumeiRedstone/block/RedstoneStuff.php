<?php
namespace Meshida\MumeiRedstone\block;

interface RedstoneStuff{
    public function onRefleshRedstoneSignal($power) : void;
}