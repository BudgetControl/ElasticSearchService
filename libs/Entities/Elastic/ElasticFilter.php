<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities\Elastic;

use BudgetcontrolLibs\ElasticSearch\Services\Transactions\Search;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class ElasticFilter
{
    private ?string $query = null;
    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?int $month = null;
    private ?int $year = null;
    private array $categories = [];
    private array $walletIds = [];
    private ?float $minAmount = null;
    private ?float $maxAmount = null;
    private ?string $type = null;
    private array $tags = [];
    private ?int $workspaceId = null;
    private ?bool $confirmed = null;
    private ?bool $planned = null;
    private ?bool $isTransfer = null;
    private ?bool $havePayee = null;
    private ?string $currency = null;
    private ?string $paymentType = null;

    private function __construct(array $filters = [])
    {
        $this->hydrate($filters);
    }

    public function hydrate(array $filters): self
    {
        foreach ($filters as $key => $value) {
            match ($key) {
                'query' => $this->setQuery($value),
                'start_date' => $this->setStartDate($value),
                'end_date' => $this->setEndDate($value),
                'month' => $this->setMonth($value),
                'year' => $this->setYear($value),
                'categories' => $this->setCategories($value),
                'wallet', 'wallet_id', 'wallet_ids' => $this->setWalletIds($value),
                'min_amount' => $this->setMinAmount($value),
                'max_amount' => $this->setMaxAmount($value),
                'type' => $this->setType($value),
                'tags' => $this->setTags($value),
                'workspace_id' => $this->setWorkspaceId($value),
                'confirmed' => $this->setConfirmed($value),
                'planned' => $this->setPlanned($value),
                'is_transfer' => $this->setIsTransfer($value),
                'have_payee' => $this->setHavePayee($value),
                'currency' => $this->setCurrency($value),
                'payment_type' => $this->setPaymentType($value),
                default => null
            };
        }
        return $this;
    }

    // Getters
    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getWalletIds(): array
    {
        return $this->walletIds;
    }

    public function getMinAmount(): ?float
    {
        return $this->minAmount;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getWorkspaceId(): ?int
    {
        return $this->workspaceId;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function getPlanned(): ?bool
    {
        return $this->planned;
    }

    public function getIsTransfer(): ?bool
    {
        return $this->isTransfer;
    }

    public function getHavePayee(): ?bool
    {
        return $this->havePayee;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    // Fluent setters
    public function setQuery(?string $query): self
    {
        $this->query = $query ? trim($query) : null;
        return $this;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function setMonth(?int $month): self
    {
        if ($month !== null && ($month < 1 || $month > 12)) {
            throw new InvalidArgumentException('Month must be between 1 and 12');
        }
        $this->month = $month;
        return $this;
    }

    public function setYear(?int $year): self
    {
        if ($year !== null && $year < 1900) {
            throw new InvalidArgumentException('Year must be greater than 1900');
        }
        $this->year = $year;
        return $this;
    }

    public function setCategories($categories): self
    {
        $this->categories = is_array($categories) 
            ? array_map('intval', $categories) 
            : array_map('intval', (array) $categories);
        return $this;
    }

    public function setWalletIds($walletIds): self
    {
        $this->walletIds = is_array($walletIds) 
            ? array_map('intval', $walletIds) 
            : array_map('intval', (array) $walletIds);
        return $this;
    }

    public function setMinAmount(?float $minAmount): self
    {
        $this->minAmount = $minAmount;
        return $this;
    }

    public function setMaxAmount(?float $maxAmount): self
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setTags($tags): self
    {
        $this->tags = is_array($tags) ? $tags : (array) $tags;
        return $this;
    }

    public function setWorkspaceId(?int $workspaceId): self
    {
        $this->workspaceId = $workspaceId;
        return $this;
    }

    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function setPlanned(?bool $planned): self
    {
        $this->planned = $planned;
        return $this;
    }

    public function setIsTransfer(?bool $isTransfer): self
    {
        $this->isTransfer = $isTransfer;
        return $this;
    }

    public function setHavePayee(?bool $havePayee): self
    {
        $this->havePayee = $havePayee;
        return $this;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    // Date range convenience methods
    public function setDateRange(?string $startDate, ?string $endDate): self
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        return $this;
    }

    public function setMonthYear(?int $month, ?int $year): self
    {
        $this->setMonth($month);
        $this->setYear($year);
        return $this;
    }

    public function setAmountRange(?float $min, ?float $max): self
    {
        $this->minAmount = $min;
        $this->maxAmount = $max;
        return $this;
    }

    // Elasticsearch query builders
    public function buildMustClauses(): array
    {
        $must = [];

        // Multi-match query per testo
        if (!empty($this->query)) {
            $must[] = [
                'multi_match' => [
                    'query' => $this->query,
                    'fields' => ['note^3', 'category_name', 'tags'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        return $must;
    }

    public function buildFilterClauses(): array
    {
        $filter = [];

        // Workspace filter (sempre obbligatorio)
        if ($this->workspaceId !== null) {
            $filter[] = [
                'term' => [
                    'workspace_id' => $this->workspaceId,
                ],
            ];
        }

        // Filtro per range di date
        if ($this->startDate || $this->endDate) {
            $range = [];
            if ($this->startDate) {
                $range['gte'] = $this->startDate;
            }
            if ($this->endDate) {
                $range['lte'] = $this->endDate;
            }

            $filter[] = [
                'range' => [
                    'date' => $range,
                ],
            ];
        }

        // Filtro per mese/anno specifico
        if ($this->month !== null && $this->year !== null) {
            $filter[] = [
                'bool' => [
                    'must' => [
                        ['term' => ['month' => $this->month]],
                        ['term' => ['year' => $this->year]],
                    ],
                ],
            ];
        }

        // Filtro per categorie
        if (!empty($this->categories)) {
            $filter[] = [
                'terms' => [
                    'category_id' => $this->categories,
                ],
            ];
        }

        // Filtro per portafogli
        if (!empty($this->walletIds)) {
            $filter[] = [
                'terms' => [
                    'wallet_id' => $this->walletIds,
                ],
            ];
        }

        // Filtro per range di importo
        if ($this->minAmount !== null || $this->maxAmount !== null) {
            $range = [];
            if ($this->minAmount !== null) {
                $range['gte'] = $this->minAmount;
            }
            if ($this->maxAmount !== null) {
                $range['lte'] = $this->maxAmount;
            }

            $filter[] = [
                'range' => [
                    'amount' => $range,
                ],
            ];
        }

        // Filtro per tipo
        if ($this->type !== null) {
            $filter[] = [
                'term' => [
                    'type' => $this->type,
                ],
            ];
        }

        // Filtro per tag
        if (!empty($this->tags)) {
            $filter[] = [
                'terms' => [
                    'tags' => $this->tags,
                ],
            ];
        }

        // Filtri boolean
        if ($this->confirmed !== null) {
            $filter[] = ['term' => ['confirmed' => $this->confirmed]];
        }

        if ($this->planned !== null) {
            $filter[] = ['term' => ['planned' => $this->planned]];
        }

        if ($this->isTransfer !== null) {
            $filter[] = ['term' => ['is_transfer' => $this->isTransfer]];
        }

        if ($this->havePayee !== null) {
            $filter[] = ['term' => ['have_payee' => $this->havePayee]];
        }

        // Filtri per currency e payment_type
        if ($this->currency !== null) {
            $filter[] = ['term' => ['currency' => $this->currency]];
        }

        if ($this->paymentType !== null) {
            $filter[] = ['term' => ['payment_type' => $this->paymentType]];
        }

        return $filter;
    }

    public function buildElasticsearchQuery(): array
    {
        return [
            'bool' => [
                'must' => $this->buildMustClauses(),
                'filter' => $this->buildFilterClauses(),
            ],
        ];
    }

    // Utility methods
    public function isEmpty(): bool
    {
        return empty(array_filter([
            $this->query,
            $this->startDate,
            $this->endDate,
            $this->month,
            $this->year,
            $this->categories,
            $this->walletIds,
            $this->minAmount,
            $this->maxAmount,
            $this->type,
            $this->tags,
            $this->confirmed,
            $this->planned,
            $this->isTransfer,
            $this->havePayee,
            $this->currency,
            $this->paymentType,
        ]));
    }

    public function hasTextSearch(): bool
    {
        return !empty($this->query);
    }

    public function hasDateFilters(): bool
    {
        return $this->startDate || $this->endDate || ($this->month && $this->year);
    }

    public function hasAmountFilters(): bool
    {
        return $this->minAmount !== null || $this->maxAmount !== null;
    }

    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'month' => $this->month,
            'year' => $this->year,
            'categories' => $this->categories,
            'wallet_ids' => $this->walletIds,
            'min_amount' => $this->minAmount,
            'max_amount' => $this->maxAmount,
            'type' => $this->type,
            'tags' => $this->tags,
            'workspace_id' => $this->workspaceId,
            'confirmed' => $this->confirmed,
            'planned' => $this->planned,
            'is_transfer' => $this->isTransfer,
            'have_payee' => $this->havePayee,
            'currency' => $this->currency,
            'payment_type' => $this->paymentType,
        ];
    }

    public static function create(): self
    {
        return new self();
    }

    public static function fromArray(array $filters): self
    {
        return new self($filters);
    }
}