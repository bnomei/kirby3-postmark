<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Postmark;
use Postmark\PostmarkClient;
use PHPUnit\Framework\TestCase;

final class PostmarkTest extends TestCase
{
    /** @var array */
    private $bot;

    private function needsAPI(): void
    {
        if (!file_exists(__DIR__ . '/site/config/config.php')) {
            $this->markTestSkipped('No config file with API-Key.');
        }
    }

    public function setUp(): void
    {
        $this->bot = ['bot' => true];
    }

    public function testPostmarkLibExists()
    {
        $this->assertIsString(PostmarkClient::class);
    }

    public function testConstruct()
    {
        $postmark = new Postmark();

        $this->assertInstanceOf(Postmark::class, $postmark);
    }

    public function testClient()
    {
        $postmark = new Postmark();

        $this->assertInstanceOf(PostmarkClient::class, $postmark->client());
    }

    public function testSingleton()
    {
        // static instance does not exists
        $postmark = Bnomei\Postmark::singleton();
        $this->assertInstanceOf(Postmark::class, $postmark);

        // static instance now does exist
        $postmark = Bnomei\Postmark::singleton();
        $this->assertInstanceOf(Postmark::class, $postmark);
    }

    public function testCallableOptions()
    {
        $postmark = new Postmark([
            'apitoken' => function () {
                return 'APITOKEN';
            },
        ]);

        $this->assertInstanceOf(Postmark::class, $postmark);
    }

    public function testSMTPTransportOptions()
    {
        $postmark = new Postmark([
            'apitoken' => 'APITOKEN',
        ]);
        $smtpTO = $postmark->transport();

        $this->assertEquals('smtp', $smtpTO['type']);
        $this->assertEquals('smtp.postmarkapp.com', $smtpTO['host']);
        $this->assertEquals(587, $smtpTO['port']);
        $this->assertEquals('tsl', $smtpTO['security']);
        $this->assertEquals(true, $smtpTO['auth']);
        $this->assertEquals('APITOKEN', $smtpTO['username']);
        $this->assertEquals('APITOKEN', $smtpTO['password']);
    }
}
