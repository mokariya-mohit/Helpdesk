<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'email' => true,
        'google_id' => true,
        'picture' => true,
        'password' => true,
        'password_set' => true,
        'smtp_password' => true,
        'api_key' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
    ];

    /**
     * Automatically hash password when setting
     */
    protected function _setPassword(?string $password): ?string
    {
        if ($password !== null && strlen($password) > 0) {
            return password_hash($password, PASSWORD_DEFAULT);
        }
        return $password;
    }

    /**
     * Encrypt a string using AES-256-CBC
     */
    public static function encryptString(?string $plain): ?string
    {
        if (empty($plain)) {
            return null;
        }
        $secret = hash('sha256', \Cake\Utility\Security::getSalt() . '_gemini_crypto_salt');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($plain, 'aes-256-cbc', $secret, 0, $iv);
        return base64_encode($iv . '::' . $encrypted);
    }

    /**
     * Decrypt an AES-256-CBC encrypted string
     */
    public static function decryptString(?string $encryptedStr): string
    {
        if (empty($encryptedStr)) {
            return '';
        }
        $data = base64_decode($encryptedStr, true);
        if (!$data || !str_contains($data, '::')) {
            return $encryptedStr;
        }
        $parts = explode('::', $data, 2);
        if (count($parts) !== 2) {
            return $encryptedStr;
        }
        [$iv, $encrypted] = $parts;
        $secret = hash('sha256', \Cake\Utility\Security::getSalt() . '_gemini_crypto_salt');
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $secret, 0, $iv);
        return $decrypted !== false ? $decrypted : $encryptedStr;
    }
}
