<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Services;

use BudgetcontrolLibs\ElasticSearch\Services\Clients\ElasticSearchClient;
use Elastic\Elasticsearch\Client;

class ElasticSearchService
{
    protected readonly Client $client;
    protected readonly string $index;

    public function __construct(ElasticSearchClient $elasticSearchClient) {
        $this->client = $elasticSearchClient->client();
        $this->index = $elasticSearchClient->indexName();
    }

    public function changeIndex(string $index): void
    {
        $this->index = $index;
    }

    
}