<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\Toolkit\A;
use Postmark\PostmarkClient;

final class Postmark
{
    /** @var PostmarkClient */
    private $client;

    /** @var array */
    private $options;

    public function __construct(array $options = [])
    {
        $defaults = [
            'debug' => option('debug'),
            // 'log' => option('bnomei.postmark.log.fn'), // TODO: logging
            'access' => option('bnomei.postmark.access'),
            'secret' => option('bnomei.postmark.secret'),
            'trap' => option('bnomei.postmark.trap'),
        ];
        $this->options = array_merge($defaults, $options);

        foreach ($this->options as $key => $callable) {
            if (is_callable($callable) && in_array($key, ['access', 'secret'])) {
                $this->options[$key] = trim((string) $callable()) . '';
            }
        }

        $this->client = new PostmarkClient(
            $this->options['access']
        );

        if ($this->option('debug')) {
            kirby()->cache('bnomei.postmark')->flush();
        }
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function option(?string $key = null)
    {
        if ($key) {
            return A::get($this->options, $key);
        }
        return $this->options;
    }

    /**
     * Get Postmark Client Instance
     *
     * @return PostmarkClient
     */
    public function client(): PostmarkClient
    {
        return $this->client;
    }

    /**
     * Get SMTP Email Transport Options Array
     *
     * @return array
     */
    public function transport(): array
    {
        return array_merge(
            [
                'username' => $this->option('access'),
                'password' => $this->option('secret'),
            ],
            option('bnomei.postmark.email.transport')
        );
    }

    public function trap(?string $email = null): ?string
    {
        return $email ?? $this->option('trap');
    }

    /** @var Postmark */
    private static $singleton;

    /**
     * @param array $options
     * @return Postmark
     */
    public static function singleton(array $options = [])
    {
        if (!self::$singleton) {
            self::$singleton = new self($options);
        }

        return self::$singleton;
    }
}
