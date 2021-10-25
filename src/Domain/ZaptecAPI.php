<?php

namespace App\Domain;

use App\Domain\Zaptec\Charger;
use App\Domain\Zaptec\Session;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ZaptecAPI
{
    public const ZAPTEC_TOKEN = '_zaptec.token';

    public function __construct(
        private HttpClientInterface $zaptecClient,
        private CacheInterface $systemCache,
        private string $zaptecUsername,
        private string $zaptecPassword,
    ) {
    }

    public function hasToken(): bool
    {
        return (bool) $this->systemCache->get(self::ZAPTEC_TOKEN, function (CacheItemInterface $item, &$save) {
            $save = false;

            return null;
        });
    }

    /**
     * @return list<Charger>
     */
    public function getChargers(): array
    {
        return $this->systemCache->get('_zaptec.chargers.v4', function (CacheItemInterface $item) {
            $item->expiresAfter(3600);

            $response = $this->zaptecClient->request('GET', '/api/chargers', ['headers' => ['Authorization' => 'Bearer '.$this->getToken()]]);

            $chargers = $response->toArray(true);
            $res = [];
            foreach ($chargers['Data'] as $c) {
                $res[] = Charger::fromArray($c);
            }

            return $res;
        });
    }

    public function getToken(): string
    {
        return (string) $this->systemCache->get(self::ZAPTEC_TOKEN, function (CacheItemInterface $item) {
            $response = $this->zaptecClient->request('POST', '/oauth/token', [
                'body' => [
                    'grant_type' => 'password',
                    'username' => $this->zaptecUsername,
                    'password' => $this->zaptecPassword,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new AccessDeniedHttpException();
            }

            $data = $response->toArray(true);
            $accessToken = $data['access_token'];
            $expiresIn = (int) $data['expires_in'];

            $item->expiresAfter($expiresIn - 10);

            return $accessToken;
        });
    }

    /**
     * @return list<Session>
     */
    public function getSessions(string $chargerId): array
    {
        return $this->systemCache->get('_zaptec.sessions.'.$chargerId, function (CacheItemInterface $item) use ($chargerId) {
            $item->expiresAfter(300);
            $res = [];

            $page = 0;
            $response = $this->zaptecClient->request('GET', "/api/chargehistory?ChargerId=$chargerId&PageSize=100&PageIndex=$page&DetailLevel=1", ['headers' => ['Authorization' => 'Bearer '.$this->getToken()]]);
            $chargers = $response->toArray(true);
            while (count($chargers['Data']) > 0) {
                ++$page;
                foreach ($chargers['Data'] as $c) {
                    $res[] = Session::fromArray($c);
                }
                $response = $this->zaptecClient->request('GET', "/api/chargehistory?ChargerId=$chargerId&PageSize=100&PageIndex=$page&DetailLevel=1", ['headers' => ['Authorization' => 'Bearer '.$this->getToken()]]);
                $chargers = $response->toArray(true);
            }

            return $res;
        });
    }
}
