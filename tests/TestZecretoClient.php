<?php

namespace Tests;

require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;
use Zekreto\Exceptions\ZekretoClientException;
use Zekreto\ZekretoClient;

class TestZecretoClient extends TestCase
{
    public function testApiCall()
    {
        $key = getenv('ZEKRETO_API_KEY');
        if ($key == false) {
            $this->markTestSkipped(
                'ZEKRETO_API_KEY is not set.'
            );
        }
        $client = new ZekretoClient($key);
        $secret = 'TESTING';
        $encrypted = $client->encrypt($secret);
        $this->assertNotEmpty($encrypted);
        $decrypted = $client->decrypt($encrypted);
        $this->assertEquals($secret, $decrypted);
    }

    public function testEmptyStringOnError()
    {
        $client = new ZekretoClient('invalid');
        $this->assertEquals("", $client->encrypt("TESTING"));
    }

    public function testExceptions()
    {
        putenv('ZEKRETO_EMPTYSTR_ON_ERROR=false');
        $client = new ZekretoClient('invalid');
        $this->expectException(ZekretoClientException::class);
        $client->encrypt('TESTING');
    }
}
