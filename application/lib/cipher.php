<?php

namespace Framework\lib;


class Cipher
{
    private $key;
    private $hmacKey;
    private $algorithm;
    private $hmacAlgorithm;
    private $initializationVectorLength;
    private $initializationVector;
    private $options = 0;

    public function __construct()
    {
        $this->key = CIPHER_KEY;
        $this->hmacKey = HMAC_KEY;
        $this->algorithm = CIPHER_ALGORITHM;
        $this->hmacAlgorithm = HMAC_ALGORITHM;
        $this->initializationVectorLength = openssl_cipher_iv_length($this->algorithm);
        $this->initializationVector = openssl_random_pseudo_bytes($this->initializationVectorLength);
    }

    public function Encrypt($text)
    {
        if ((in_array($this->algorithm, openssl_get_cipher_methods())) &&
            ($text !== '') &&
            ($this->key != '')
        ) {
            $text = serialize($text);
            $ciphered = $this->initializationVector . openssl_encrypt($text, $this->algorithm, $this->key, $this->options, $this->initializationVector);
            return base64_encode($ciphered);
        } else {
            return false;
        }
    }

    public function Decrypt($ciphertext)
    {
        if ((in_array($this->algorithm, openssl_get_cipher_methods())) &&
            ($ciphertext !== '') &&
            ($this->key != '')
        ) {
            $ciphertext = base64_decode($ciphertext);
            $text = openssl_decrypt(substr($ciphertext, $this->initializationVectorLength),
                $this->algorithm, $this->key, $this->options,
                substr($ciphertext, 0, $this->initializationVectorLength));
            return unserialize($text);
        } else {
            return false;
        }
    }

    public function Hash($text) {
        if (($this->hmacAlgorithm !== '') && ($this->hmacKey !== '')) {
            return hash_hmac($this->hmacAlgorithm, $text, $this->hmacKey);
        }
    }

    public function CompareHash($hashed, $string)
    {
        return (hash_equals($hashed, $this->Hash($string))) ? true : false;
    }
}
