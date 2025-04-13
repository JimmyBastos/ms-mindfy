<?php

namespace App\Services;

use App\Dtos\Integrations\EventData;
use App\Dtos\Integrations\IntegrationData;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\KeyValueHttpHeader;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;

class GenerationService
{
    public function __construct()
    {
    }

    public function client(): PendingRequest
    {
        return Http::baseUrl(Config::get('services.n8n.url'))
            ->timeout(300)
            ->retry(2)
            ->asJson();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function generateContent(array $query = []): array
    {
        return $this->client()
            ->post('/2d1c13c9-196c-45cd-a14e-d2e39c370253/books/search', $query)
            ->throw()
            ->json();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function generateContentOutline(array $content = []): Collection
    {
        return $this->client()
            ->post('/6572adda-6454-4209-a09c-edd7ac5b9ce0/contents/outline', $content)
            ->throw()
            ->collect();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function expandContentOutline(array $query = []): Collection
    {
        return $this->client()
            ->post('/6572adda-6454-4209-a09c-edd7ac5b9ce0/contents/expand', $query)
            ->throw()
            ->collect();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function generateAudioSpeech(array $data = []): string
    {
        return $this->client()
            ->post('/6700d0e3-d206-4f1e-a82a-393809078db2/audio/speech',
                ['lang' => $data['lang'], 'text' => $data['text']])
            ->throw()->body();
    }
}
