<?php
namespace App\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

final class PasswordEncoder extends BasePasswordEncoder implements PasswordEncoderInterface
{
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
        return $this->BCryptPasswordEncoder->encodePassword($raw, $salt);
    }
    
    
    public function isPasswordValid($encoded, $raw, $salt)
    {
        
        // Right bcrypt password ?
        if ($this->BCryptPasswordEncoder->isPasswordValid($encoded, $raw, $salt)) {
            return true;
        }
    
        // Wrong bcrypt password ?
        if (substr($encoded, 0, 4) === '$2y$') {
            return false;
        }
        
        // Is it an plain old sha1 ?
        if(!$this->isPasswordTooLong($raw) && $encoded === sha1($raw)) {
            return true;
        }
        
        // Nope
        return false;
    }
}