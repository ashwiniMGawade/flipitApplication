<?php
namespace Core\Domain\Adapter;

interface PurifierInterface
{
    public function purify($params);
}
