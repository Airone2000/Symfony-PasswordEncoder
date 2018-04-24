<?php
namespace App\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

final class PasswordEncoder extends BasePasswordEncoder implements PasswordEncoderInterface
{
    
    private $sha1 = null;
    
    /**
     * @var \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder
     */
    private $BCryptPasswordEncoder;
    
    function __construct(BCryptPasswordEncoder $BCryptPasswordEncoder)
    {
        $this->BCryptPasswordEncoder = $BCryptPasswordEncoder;
    }
    
    /**
     * Encodes the raw password.
     *
     * @param string $raw The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword($raw, $salt)
    {
        $this->sha1 = sha1($raw);
        return $this->BCryptPasswordEncoder->encodePassword($raw, $salt);
    }
    
    
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($this->BCryptPasswordEncoder->isPasswordValid($encoded, $raw, $salt)) {
            return true;
        }
    
        // prevent legacy fallback when it's obvious that the password
        // has been hashed using bcrypt (hash starts with '$2y$')
        if (substr($encoded, 0, 4) === '$2y$') {
            return false;
        }
        
        // Old sha1
        if(!$this->isPasswordTooLong($raw) && $encoded === sha1($raw)) {
            return true;
        }
        
        
        return false;
    }
}