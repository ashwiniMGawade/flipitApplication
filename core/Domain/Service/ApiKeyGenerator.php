<?php
namespace Core\Domain\Service;

class ApiKeyGenerator
{
    public function generate($length = 32)
    {
        $apiKey = '';
        if ($length > 7 && $length < 65) {
            $allowedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_';
            $charactersLength = strlen($allowedCharacters);
            for ($i = 0; $i < $length; $i++) {
                $apiKey .= $allowedCharacters[rand(0, $charactersLength - 1)];
            }
        } else {
            throw new \Exception('The API Key length must be between 8 and 64 characters');
        }
        return $apiKey;
    }
}
