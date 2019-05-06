<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database;

class Transaction
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var string[]
     */
    private $etags;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->etags = [];
    }

    public function snapshot(Reference $reference): Snapshot
    {
        $uri = (string) $reference->getUri();

        $result = $this->apiClient->getWithETag($uri);

        $this->etags[$uri] = $result['etag'];

        return new Snapshot($reference, $result['value']);
    }

    public function set(Reference $reference, $value)
    {
        $etag = $this->getEtagForReference($reference);

        $this->apiClient->setWithEtag($reference->getUri(), $value, $etag);
    }

    private function getEtagForReference(Reference $reference)
    {
        $uri = (string) $reference->getUri();

        return $this->etags[$uri] ?? '';
    }
}
