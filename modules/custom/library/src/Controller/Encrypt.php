<?php

namespace Drupal\library\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Site\Settings;


class Encrypt
{

    private $encrypt_method = "AES-256-CBC";
    private $secret_iv = 'xsmind';

    public function encode($data)
    {
        $secret_key = settings::get('secret_key');
        $key = hash('sha256', (string)$secret_key);
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
        $encoded_data = base64_encode(openssl_encrypt($data, $this->encrypt_method, $key, 0, $iv));
        return $encoded_data;
    }

    public function decode($encoded_data)
    {
        $secret_key = settings::get('secret_key');
        $key = hash('sha256', (string)$secret_key);
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);
        $data = openssl_decrypt(base64_decode($encoded_data), $this->encrypt_method, $key, 0, $iv);
        return $data;
    }

}
