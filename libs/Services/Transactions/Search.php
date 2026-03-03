<?php
declare(strict_types=1);
namespace BudgetcontrolLibs\ElasticSearch\Services\Transactions;

use BudgetcontrolLibs\ElasticSearch\Entities\AggregationTransactions;
use BudgetcontrolLibs\ElasticSearch\Entities\Elastic\ElasticAggregator;
use BudgetcontrolLibs\ElasticSearch\Entities\Elastic\ElasticFilter;
use BudgetcontrolLibs\ElasticSearch\Entities\Transactions\EntryTransaction;
use BudgetcontrolLibs\ElasticSearch\Services\ElasticSearchService;
use Illuminate\Support\Facades\Log;

class Search extends ElasticSearchService
{

    /**
     * Search for transactions based on the provided filters
     * 
     * @param array|ElasticFilter $filters Array of search criteria or ElasticFilter object
     * @param int $from Starting offset for pagination (default: 0)
     * @param int $size Number of results to return (default: 50)
     * @return ElasticFilter containing total count and EntryTransaction objects
     */
    public function search(ElasticFilter $filters, int $from = 0, int $size = 50): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => $filters->buildElasticsearchQuery(),
                'sort' => [
                    ['date' => ['order' => 'desc']],
                    ['timestamp' => ['order' => 'desc']],
                ],
                'from' => $from,
                'size' => $size,
            ],
        ];

        $response = $this->client->search($params);

        try {
            $transactions = [];
            foreach ($response['hits']['hits'] as $hit) {
                $transactions[] = EntryTransaction::fromElasticsearchResult($hit);
            }

            return [
                'total' => $response['hits']['total']['value'],
                'transactions' => $transactions,
                'from' => $from,
                'size' => $size,
            ];
        } catch (\Exception $e) {
            Log::error('Errore durante la conversione dei risultati di ricerca', [
                'error' => $e->getMessage(),
                'filters' => $filters->toArray(),
            ]);
            return [
                'total' => 0,
                'transactions' => [],
                'from' => $from,
                'size' => $size,
            ];
        }
    }

    /**
     * Ricerca avanzata per espressioni tipo "cena > 50" o "supermercato marzo"
     */
    public function smartSearch(string $query, int $workspaceId): array
    {
        $filter = ElasticFilter::create()
            ->setQuery($query)
            ->setWorkspaceId($workspaceId);

        return $this->search($filter);
    }

    /**
     * Execute aggregation query using ElasticAggregator
     * @return array <Int:AggregationTransactions> Returns an array of aggregation results based on the provided ElasticAggregator configuration.
     */
    public function aggregate(ElasticAggregator $aggregator): array
    {
        $body = $aggregator->buildQuery();

        $params = [
            'index' => $this->index,
            'body' => $body,
        ];

        $response = $this->client->search($params);

        try {
            $results = [
                'total' => $response['hits']['total']['value'] ?? 0,
                'aggregations' => $response['aggregations'] ?? [],
            ];

            $aggregationType = $aggregator->type ?? null;
            if (is_null($aggregationType)) {
                throw new \Exception('Tipo di aggregazione non specificato nell\'aggregatore');
            }
            return AggregationTransactions::$aggregationType($results['aggregations']);

        } catch (\Exception $e) {
            Log::error('Errore durante l\'esecuzione dell\'aggregazione', [
                'error' => $e->getMessage(),
                'body' => $body,
            ]);
            return [
                'total' => 0,
                'aggregations' => [],
            ];
        }
    }

}