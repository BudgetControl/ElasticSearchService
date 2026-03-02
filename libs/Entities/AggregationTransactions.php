<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities;

use JsonSerializable;

class AggregationTransactions implements JsonSerializable
{
    private \stdClass $aggregations;

    public function __construct(\stdClass $data)
    {
        $this->aggregations = $data;
    }

    public function aggregations(): \stdClass
    {
        return $this->aggregations;
    }

    /**
     * Aggregates transaction data by category.
     *
     * This method processes an array of transaction data and groups/aggregates
     * the transactions based on their category classification.
     *
     * @param array $data The array of transaction data to be aggregated by category
     * @return array The aggregated transaction data grouped by category
     */
    public static function byCategory(array $data): array
    {
        $aggregations = [];
        foreach($data['by_category']['buckets'] as $bucket) {
            $object = new \stdClass();
            $object->category_slug = $bucket['category_slug']['buckets'][0]['key'] ?? null;
            $object->category_name = $bucket['category_name']['buckets'][0]['key'] ?? null;
            $object->total = $bucket['total_amount']['value'] ?? null;
            $object->category_id = $bucket['category_id']['buckets'][0]['key'] ?? null;

            $aggregations[] = new self($object);
        }

        return $aggregations;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

}