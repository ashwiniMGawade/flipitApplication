<?php
namespace Core\Domain\Service;

class KeyGenerator
{
    public function generate($length = 32)
    {
        $key = '';
        if ($length > 7 && $length < 65) {
            $allowedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_';
            $charactersLength = strlen($allowedCharacters);
            for ($i = 0; $i < $length; $i++) {
                $key .= $allowedCharacters[rand(0, $charactersLength - 1)];
            }
        } else {
            throw new \Exception('The key length must be between 8 and 64 characters');
        }
        return $key;
    }
}
