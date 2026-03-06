<?php
declare(strict_types=1);
namespace BudgetcontrolLibs\ElasticSearch\Services\Transactions;

use Budgetcontrol\Library\Model\Entry;
use Budgetcontrol\Library\Model\EntryInterface;
use Budgetcontrol\Library\Model\Wallet;
use BudgetcontrolLibs\ElasticSearch\Entities\Transactions\EntryTransaction;
use BudgetcontrolLibs\ElasticSearch\Services\ElasticSearchService;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class Indexer extends ElasticSearchService
{

    /**
     * Indexes a transaction entry into the search engine.
     *
     * This method takes a transaction entry and processes it for indexing,
     * making it searchable within the system. The indexing process typically
     * involves extracting relevant data from the entry and storing it in
     * an optimized format for quick retrieval.
     *
     * @param Entry $entry The transaction entry to be indexed
     * @return void
     * @throws ElasticsearchException If the indexing process fails
     */public function indexTransaction(Entry $entry): void
    {
        $params = [
            'index' => $this->index,
            'uuid' => $entry->uuid,
            'body' => $this->buildEntry($entry),
        ];
        
        $response = $this->client->index($params);
        $isError = $response['errors'] ?? false;

        if ($isError) {
            Log::error('Bulk indexing encountered errors', ['response' => $response]);
            throw new ServerResponseException('Bulk indexing failed with errors');
        } else {
            Log::info('Bulk indexing completed successfully', ['response' => $response]);
        }
    }
    
    /**
     * Bulk transactions
     * @param array<int:Entry> $entrys
     * @return void
     */
    public function bulkIndexTransactions(Collection $entrys): void
    {
        $params = ['body' => []];
        
        foreach ($entrys as $entry) {
            if(!$entry instanceof Entry) {
                Log::warning("Is not a Entry instance", ['entry' => $entry]);
                continue; // Salta se non è un'istanza di Entry
            }

            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                ],
            ];
            
            $params['body'][] = $this->buildEntry($entry);
        }
        
        $response = $this->client->bulk($params);
        $isError = $response['errors'] ?? false;

        if ($isError) {
            Log::error('Bulk indexing encountered errors', ['response' => $response]);
            throw new ServerResponseException('Bulk indexing failed with errors');
        } else {
            Log::info('Bulk indexing completed successfully', ['response' => $response]);
        }
    }

    /**
     * Builds an array representation of an Entry for indexing purposes.
     *
     * This method transforms an Entry object into a structured array format
     * that can be used for search indexing or data storage operations.
     *
     * @param EntryInterface $entry The entry object to be converted to array format
     * @return array The structured array representation of the entry
     */
    protected function buildEntry(EntryInterface $entry): array
    {
        $transaction = new EntryTransaction($entry);
        return $transaction->toArray();
    }

    /**
     * Indexes a wallet document in Elasticsearch.
     *
     * This method takes a Wallet object and creates or updates its corresponding
     * document in the Elasticsearch index. The wallet data will be processed and
     * stored in a format suitable for search and aggregation operations.
     *
     * @param Collection $wallet The wallet object to be indexed in Elasticsearch
     * @return void
     * @throws \Exception If the indexing operation fails or Elasticsearch is unavailable
     */
    public function bulkIndexWallets(Collection $wallets): void
    {
        $params = ['body' => []];

        foreach ($wallets as $wallet) {
            if(!$wallet instanceof Wallet) {
                Log::warning("Is not a Wallet instance", ['wallet' => $wallet]);
                continue; // Skip if it's not an instance of Wallet
            }

            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                ],
            ];

            $params['body'][] = $this->buildEntry($wallet);

        }
        
        
        try {
            $response = $this->client->bulk($params);
            $isError = $response['errors'] ?? false;

            if ($isError) {
                Log::error('Bulk indexing of wallets encountered errors', ['response' => $response]);
                throw new ServerResponseException('Bulk indexing of wallets failed with errors');
            } else {
                Log::info('Bulk indexing of wallets completed successfully', ['response' => $response]);
            }
        } catch (ElasticsearchException $e) {
            Log::error('Elasticsearch exception during bulk indexing of wallets', ['error' => $e->getMessage()]);
            throw $e;
        }

    }
}