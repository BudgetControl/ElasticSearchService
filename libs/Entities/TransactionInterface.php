<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities;

use Budgetcontrol\Library\Model\EntryInterface;

interface TransactionInterface
{
    /**
     * Gets the unique identifier (UUID) of the transaction.
     *
     * @return string The transaction UUID
     */
    public function getUuid(): string;

    /**
     * Sets the unique identifier (UUID) of the transaction.
     *
     * @param string $uuid The UUID to set
     * @return self Returns the current instance for method chaining
     */
    public function setUuid(string $uuid): self;

    /**
     * Returns the Elasticsearch mapping configuration for the transaction entity.
     * Defines how the entity fields should be mapped in Elasticsearch.
     *
     * @return array The Elasticsearch mapping configuration
     */
    public static function mapping(): array;

    /**
     * Creates a new transaction instance from Elasticsearch search result.
     * Transforms Elasticsearch hit data into a transaction entity.
     *
     * @param array $hit The Elasticsearch hit data containing _source and metadata
     * @return self A new transaction instance populated with the hit data
     */
    public static function fromElasticsearchResult(array $hit): self;

    /**
     * Creates a new transaction instance from an associative array.
     * Transforms array data into a transaction entity.
     *
     * @param array $data The associative array containing transaction data
     * @return self A new transaction instance populated with the array data
     */
    public static function fromArray(array $data): self;

    /**
     * Converts the transaction entity to an associative array.
     * Transforms the entity properties into a key-value array format.
     *
     * @return array The transaction data as an associative array
     */
    public function toArray(): array;

    /**
     * Populates the current transaction instance with data from an array.
     * Updates the entity properties with the provided data.
     *
     * @param EntryInterface $data The associative array containing transaction data
     * @return self Returns the current instance for method chaining
     */
    public function hydrate(EntryInterface $data): self;


}