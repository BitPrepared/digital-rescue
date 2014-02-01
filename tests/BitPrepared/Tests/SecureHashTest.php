<?php

namespace BitPrepared\Tests;

class SecureHashTest extends \PHPUnit_Framework_TestCase
{
    
    protected function setUp()
    {
        
    }

    public function testGenerazioneHashDiversi()
    {

		$secure = new \BitPrepared\Security\SecureHash();
		$pass = 'example p@ssword'; // the password to encrypt
		$salt = ''; // salt is passed by reference and generated on the fly
		$encrypted = $secure->create_hash($pass, $salt); // the encrypted version of the password (for database storage)

		$salt2 = '';
		$encrypted2 = $secure->create_hash($pass, $salt2); // the encrypted version of the password (for database storage)

		$this->assertTrue($salt != $salt2);

    }

    public function testGenerazione() {
    	$secure = new \BitPrepared\Security\SecureHash();
		$pass = 'example p@ssword'; // the password to encrypt
		$salt = ''; // salt is passed by reference and generated on the fly
		$encrypted = $secure->create_hash($pass, $salt); // the encrypted version of the password (for database storage)
		$this->assertTrue($secure->validate_hash($pass, $encrypted, $salt));
    }

}