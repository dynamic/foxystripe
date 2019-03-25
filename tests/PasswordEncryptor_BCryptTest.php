<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Security\PasswordEncryptor_BCrypt;
use SilverStripe\Dev\FunctionalTest;

/**
 * Class PasswordEncryptor_BCryptTest
 * @package Dynamic\FoxyStripe\Test
 */
class PasswordEncryptor_BCryptTest extends FunctionalTest
{
    /**
     *
     */
    public function testGetCost()
    {
        $encryptor = new PasswordEncryptor_BCrypt();

        $this->assertEquals(10, $encryptor::get_cost());
    }

    /**
     *
     */
    public function testSetCost()
    {
        $encryptor = new PasswordEncryptor_BCrypt();

        $original = $encryptor::get_cost();

        $encryptor::set_cost(15);

        $this->assertEquals(15, $encryptor::get_cost());
    }

    /**
     *
     */
    public function testEncrypt()
    {
        $encryptor = new PasswordEncryptor_BCrypt();

        $pass = 'foobarbaz';

        $expected = password_hash($pass, PASSWORD_BCRYPT, ['cost' => $encryptor::get_cost()]);

        $this->assertTrue(password_verify($pass, $expected));
    }
}
