<?php

namespace App\Domain;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ZaptecAPI
{
    const ZAPTEC_TOKEN = "_zaptec.token";

    public function __construct(
        private HttpClientInterface $zaptecClient,
        private CacheInterface $systemCache,
        private string $zaptecUsername,
        private string $zaptecPassword,
    )
    {
    }

    public function getToken(): string
    {
        return (string)$this->systemCache->get(self::ZAPTEC_TOKEN, function(CacheItemInterface $item) {
            $response = $this->zaptecClient->request("POST", "/oauth/token", [
                "body" => [
                    "grant_type" => "password",
                    "username"   => $this->zaptecUsername,
                    "password"   => $this->zaptecPassword,
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new AccessDeniedHttpException();
            }

            $data        = $response->toArray(true);
            $accessToken = $data["access_token"];
            $expiresIn   = (int)$data['expires_in'];

            $item->expiresAfter($expiresIn - 10);
            return $accessToken;
        });
    }

    public function hasToken(): bool
    {
        return (bool)$this->systemCache->get(self::ZAPTEC_TOKEN, function(CacheItemInterface $item, &$save) {
            $save = false;
            return null;
        });
    }
}

