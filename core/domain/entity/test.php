<?php
namespace domain\Entity;
class Test
{
    public static function showText($name)
    {
        return "Hello ". $name;
    }
}