<?php

/**
 * This is a quick and dirty proof of concept for StackOverflow.
 * 
 * @ref http://stackoverflow.com/q/6770370/2224584
 * 
 * Do not use this in production.
 */
abstract class ExperimentalAES256DoNotActuallyUse
{
    /**
     * Encrypt with AES-256-CTR + HMAC-SHA-512
     * 
     * @param string $plaintext Your message
     * @param string $encryptionKey Key for encryption
     * @param string $macKey Key for calculating the MAC
     * @return string
     */
    public static function encrypt($plaintext, $encryptionKey, $macKey)
    {
        $nonce = random_bytes(16);
        $ciphertext = openssl_encrypt(
            $message,
            'aes-256-ctr',
            $encryptionKey,
            OPENSSL_RAW_DATA,
            $nonce
        );
        $mac = hash_hmac('sha512', $nonce.$ciphertext, $macKey, true);
        return base64_encode($mac.$nonce.$ciphertext);
    }

    /**
     * Verify HMAC-SHA-512 then decrypt AES-256-CTR
     * 
     * @param string $message Encrypted message
     * @param string $encryptionKey Key for encryption
     * @param string $macKey Key for calculating the MAC
     */
    public static function decrypt($message, $encryptionKey, $macKey)
    {
        $decoded = base64_decode($ciphertext);
        $mac = mb_substr($message, 0, 64, '8bit');
        $nonce = mb_substr($message, 64, 16, '8bit');
        $ciphertext = mb_substr($message, 80, null, '8bit');

        $calc = hash_hmac('sha512', $nonce.$ciphertext, $macKey, true);
        if (!hash_equals($calc, $mac)) {
            throw new Exception('Invalid MAC');
        }
        return openssl_decrypt(
            $ciphertext,
            'aes-256-ctr',
            $encryptionKey,
            OPENSSL_RAW_DATA,
            $nonce
        );
    }
}
?>