<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities\Elastic;

use BudgetcontrolLibs\ElasticSearch\Entities\Elastic\ElasticFilter;

final class ElasticAggregator
{
    private array $aggregations = [];
    private ?ElasticFilter $filter = null;
    public ?string $type = null;

    public function __construct(?ElasticFilter $filter = null)
    {
        $this->filter = $filter;
    }

    // Factory methods
    public static function create(?ElasticFilter $filter = null): self
    {
        return new self($filter);
    }

    public function setFilter(ElasticFilter $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    // Sum aggregations
    public function sumByField(string $field, string $name = null): self
    {
        $this->type = 'sumByField';
        $name = $name ?: "sum_of_{$field}";
        $this->aggregations[$name] = [
            'sum' => ['field' => $field]
        ];
        return $this;
    }

    public function totalAmount(string $name = 'total_amount'): self
    {
        return $this->sumByField('amount', $name);
    }

    // Average aggregations
    public function avgByField(string $field, string $name = null): self
    {
        $name = $name ?: "avg_of_{$field}";
        $this->aggregations[$name] = [
            'avg' => ['field' => $field]
        ];
        return $this;
    }

    public function averageAmount(string $name = 'avg_amount'): self
    {
        return $this->avgByField('amount', $name);
    }

    // Count aggregations
    public function countDocuments(string $name = 'total_count'): self
    {
        $this->aggregations[$name] = [
            'value_count' => ['field' => 'uuid']
        ];
        return $this;
    }

    public function countUniqueValues(string $field, string $name = null): self
    {
        $name = $name ?: "unique_{$field}";
        $this->aggregations[$name] = [
            'cardinality' => ['field' => $field]
        ];
        return $this;
    }

    // Stats aggregations (min, max, avg, sum, count)
    public function statsForField(string $field, string $name = null): self
    {
        $name = $name ?: "stats_of_{$field}";
        $this->aggregations[$name] = [
            'stats' => ['field' => $field]
        ];
        return $this;
    }

    public function amountStats(string $name = 'amount_stats'): self
    {
        return $this->statsForField('amount', $name);
    }

    // Terms aggregations (group by)
    public function groupBy(string $field, int $size = 1000, string $name = null, array $subAggs = []): self
    {
        $name = $name ?: "group_by_{$field}";
        
        $aggregation = [
            'terms' => [
                'field' => $field,
                'size' => $size,
                'order' => ['_count' => 'desc']
            ]
        ];

        if (!empty($subAggs)) {
            $aggregation['aggs'] = $subAggs;
        }

        $this->aggregations[$name] = $aggregation;
        return $this;
    }

    public function groupByCategory(int $size = 1000, string $name = 'by_category'): self
    {
        $this->type = 'byCategory';
        $subAggs = [
            'category_name' => [
                'terms' => ['field' => 'category_name', 'size' => 1]
            ],
            'category_id' => [
                'terms' => ['field' => 'category_id', 'size' => 1]
            ],
            'category_slug' => [
                'terms' => ['field' => 'category_slug', 'size' => 1]
            ],
            'total_amount' => [
                'sum' => ['field' => 'amount']
            ],
            'avg_amount' => [
                'avg' => ['field' => 'amount']
            ],
            'count' => [
                'value_count' => ['field' => 'uuid']
            ]
        ];

        return $this->groupBy('category_id', $size, $name, $subAggs);
    }

    public function groupByWallet(int $size = 1000, string $name = 'by_wallet'): self
    {
        $subAggs = [
            'total_amount' => [
                'sum' => ['field' => 'amount']
            ],
            'avg_amount' => [
                'avg' => ['field' => 'amount']
            ],
            'count' => [
                'value_count' => ['field' => 'uuid']
            ]
        ];

        return $this->groupBy('wallet_id', $size, $name, $subAggs);
    }

    public function groupByType(string $name = 'by_type'): self
    {
        $subAggs = [
            'total_amount' => [
                'sum' => ['field' => 'amount']
            ],
            'count' => [
                'value_count' => ['field' => 'uuid']
            ]
        ];

        return $this->groupBy('type', 10, $name, $subAggs);
    }

    public function groupByPaymentType(string $name = 'by_payment_type'): self
    {
        $subAggs = [
            'total_amount' => [
                'sum' => ['field' => 'amount']
            ],
            'count' => [
                'value_count' => ['field' => 'uuid']
            ]
        ];

        return $this->groupBy('payment_type', 20, $name, $subAggs);
    }

    public function groupByCurrency(string $name = 'by_currency'): self
    {
        $subAggs = [
            'total_amount' => [
                'sum' => ['field' => 'amount']
            ],
            'count' => [
                'value_count' => ['field' => 'uuid']
            ]
        ];

        return $this->groupBy('currency', 10, $name, $subAggs);
    }

    // Date aggregations
    public function dateHistogram(string $field = 'date', string $interval = 'month', string $name = null): self
    {
        $name = $name ?: "by_{$interval}";
        
        $this->aggregations[$name] = [
            'date_histogram' => [
                'field' => $field,
                'calendar_interval' => $interval,
                'format' => 'yyyy-MM-dd',
                'order' => ['_key' => 'asc']
            ],
            'aggs' => [
                'total_amount' => [
                    'sum' => ['field' => 'amount']
                ],
                'count' => [
                    'value_count' => ['field' => 'uuid']
                ]
            ]
        ];
        
        return $this;
    }

    public function monthlyTrend(string $name = 'monthly_trend'): self
    {
        return $this->dateHistogram('date', 'month', $name);
    }

    public function dailyTrend(string $name = 'daily_trend'): self
    {
        return $this->dateHistogram('date', 'day', $name);
    }

    public function weeklyTrend(string $name = 'weekly_trend'): self
    {
        return $this->dateHistogram('date', 'week', $name);
    }

    public function yearlyTrend(string $name = 'yearly_trend'): self
    {
        return $this->dateHistogram('date', 'year', $name);
    }

    // Range aggregations
    public function amountRanges(array $ranges, string $name = 'amount_ranges'): self
    {
        $this->aggregations[$name] = [
            'range' => [
                'field' => 'amount',
                'ranges' => $ranges
            ],
            'aggs' => [
                'count' => [
                    'value_count' => ['field' => 'uuid']
                ]
            ]
        ];
        return $this;
    }

    public function amountBuckets(string $name = 'amount_buckets'): self
    {
        $ranges = [
            ['to' => 10, 'key' => 'small'],
            ['from' => 10, 'to' => 50, 'key' => 'medium'],
            ['from' => 50, 'to' => 100, 'key' => 'large'],
            ['from' => 100, 'key' => 'very_large']
        ];
        
        return $this->amountRanges($ranges, $name);
    }

    // Boolean aggregations
    public function booleanStats(string $field, string $name = null): self
    {
        $name = $name ?: "stats_{$field}";
        
        $this->aggregations[$name] = [
            'terms' => [
                'field' => $field,
                'size' => 2
            ],
            'aggs' => [
                'count' => [
                    'value_count' => ['field' => 'uuid']
                ],
                'percentage' => [
                    'bucket_script' => [
                        'buckets_path' => [
                            'count' => '_count',
                            'total' => '_count'
                        ],
                        'script' => 'params.count / params.total * 100'
                    ]
                ]
            ]
        ];
        
        return $this;
    }

    public function confirmedStats(string $name = 'confirmed_stats'): self
    {
        return $this->booleanStats('confirmed', $name);
    }

    public function plannedStats(string $name = 'planned_stats'): self
    {
        return $this->booleanStats('planned', $name);
    }

    public function transferStats(string $name = 'transfer_stats'): self
    {
        return $this->booleanStats('is_transfer', $name);
    }

    // Complex aggregations
    public function categoryWithTimebreakdown(string $name = 'category_time_breakdown'): self
    {
        $this->aggregations[$name] = [
            'terms' => [
                'field' => 'category_id',
                'size' => 50
            ],
            'aggs' => [
                'category_name' => [
                    'terms' => ['field' => 'category_name', 'size' => 1]
                ],
                'by_month' => [
                    'date_histogram' => [
                        'field' => 'date',
                        'calendar_interval' => 'month',
                        'format' => 'yyyy-MM'
                    ],
                    'aggs' => [
                        'total' => [
                            'sum' => ['field' => 'amount']
                        ]
                    ]
                ],
                'total_amount' => [
                    'sum' => ['field' => 'amount']
                ]
            ]
        ];
        
        return $this;
    }

    // Custom aggregations
    public function addCustomAggregation(string $name, array $aggregation): self
    {
        $this->aggregations[$name] = $aggregation;
        return $this;
    }

    // Build final query
    public function buildQuery(bool $includeHits = false, int $size = 0): array
    {
        $query = [
            'size' => $includeHits ? $size : 0,
            'aggs' => $this->aggregations
        ];

        if ($this->filter && !$this->filter->isEmpty()) {
            $query['query'] = $this->filter->buildElasticsearchQuery();
        }

        return $query;
    }

    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    public function hasAggregations(): bool
    {
        return !empty($this->aggregations);
    }

    public function reset(): self
    {
        $this->aggregations = [];
        return $this;
    }

    // Preset combinations
    public function financialSummary(string $prefix = ''): self
    {
        $prefix = $prefix ? "{$prefix}_" : '';
        
        return $this
            ->totalAmount("{$prefix}total_amount")
            ->averageAmount("{$prefix}avg_amount")
            ->amountStats("{$prefix}amount_stats")
            ->countDocuments("{$prefix}transaction_count")
            ->groupByType("{$prefix}by_type")
            ->groupByCategory(20, "{$prefix}by_category")
            ->monthlyTrend("{$prefix}monthly_trend");
    }

    public function categoryAnalysis(): self
    {
        return $this
            ->groupByCategory(100, 'category_breakdown')
            ->categoryWithTimebreakdown('category_time_analysis');
    }

    public function paymentAnalysis(): self
    {
        return $this
            ->groupByPaymentType('payment_type_breakdown')
            ->groupByCurrency('currency_breakdown')
            ->groupByWallet(50, 'wallet_breakdown');
    }

    public function timeAnalysis(): self
    {
        return $this
            ->monthlyTrend('monthly_analysis')
            ->weeklyTrend('weekly_analysis')
            ->dailyTrend('daily_analysis');
    }

    public function behaviorAnalysis(): self
    {
        return $this
            ->confirmedStats('confirmed_analysis')
            ->plannedStats('planned_analysis')
            ->transferStats('transfer_analysis')
            ->amountBuckets('amount_distribution');
    }
}