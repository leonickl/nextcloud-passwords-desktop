<?php

namespace App;

use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Cache;
use SensitiveParameterValue;

class Crypto
{
    private static self $instance;

    private function __construct(private readonly array $keychain)
    {
        foreach ($this->keychain as $type => $value) {
            foreach ($this->decryptKeychain($value)['keys'] as $uuid => $key) {
                $this->decryptedKeychain[$type][$uuid] = hex2bin($key);
            }
        }
    }

    public static function init(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self(self::keychain());
        }

        return self::$instance;
    }

    private array $decryptedKeychain = [];

    private static function keychain(): array
    {
        $keys = Cache::get('keys');

        if (!is_object($keys)) {
            throw new UnauthorizedException("No keys found. Login again.");
        }

        return (array)$keys;
    }

    public function decrypt(array|object $object, array $fields): array
    {
        $object = (array)$object;

        $key = $this->decryptedKeychain[$object['cseType']][$object['cseKey']];

        $result = [];

        foreach ($fields as $field) {
            $result[$field] = $this->decryptBinaryString(hex2bin($object[$field]), $key);
        }

        return $result;
    }

    public static function solveChallenge(SensitiveParameterValue $password, array $salts): string
    {
        $passwordSalt = sodium_hex2bin($salts[0]);
        $genericHashKey = sodium_hex2bin($salts[1]);
        $passwordHashSalt = sodium_hex2bin($salts[2]);

        $genericHash = sodium_crypto_generichash(
            $password->getValue() . $passwordSalt,
            $genericHashKey,
            SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
        );

        $passwordHash = sodium_crypto_pwhash(
            SODIUM_CRYPTO_BOX_SEEDBYTES,
            $genericHash,
            $passwordHashSalt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );

        return sodium_bin2hex($passwordHash);
    }

    private function decryptBinaryString(string $binaryString, string $key): string
    {
        if (strlen($binaryString) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            return "";
        }

        $nonce = substr($binaryString, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $ciphertext = substr($binaryString, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $decrypted = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);

        if ($decrypted === false) {
            throw new UnauthorizedException("Encryption failed.");
        }

        return $decrypted;
    }

    private function decryptKeychain(string $encryptedKeychainHex): array
    {
        $encryptedKeychain = hex2bin($encryptedKeychainHex);

        $salt = substr($encryptedKeychain, 0, SODIUM_CRYPTO_PWHASH_SALTBYTES);
        $payload = substr($encryptedKeychain, SODIUM_CRYPTO_PWHASH_SALTBYTES);

        $decryptionKey = sodium_crypto_pwhash(
            SODIUM_CRYPTO_BOX_SEEDBYTES,
            Master::ask()->getValue(),
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        $nonce = substr($payload, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($payload, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $decrypted = sodium_crypto_secretbox_open($ciphertext, $nonce, $decryptionKey);

        if (!$decrypted) {
            throw new UnauthorizedException("Decryption failed.");
        }

        return json_decode($decrypted, true);
    }
}
