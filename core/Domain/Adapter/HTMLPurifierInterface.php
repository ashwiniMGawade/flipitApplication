<?php
namespace Core\Domain\Adapter;

interface HTMLPurifierInterface
{
    public function purify($params);
}
