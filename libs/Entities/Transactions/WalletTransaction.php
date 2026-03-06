<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities\Transactions;

use Budgetcontrol\Library\Model\EntryInterface;
use BudgetcontrolLibs\ElasticSearch\Entities\TransactionInterface;
use JsonSerializable;

class EntryTransaction implements JsonSerializable, TransactionInterface
{
    protected string $uuid;
    protected string $name;
    protected string $type;
    protected ?int $installement = null;
    protected ?float $installmentValue = null;
    protected ?float $creditLimit = null;
    protected string $currency;
    protected float $balance = 0;
    protected bool $excludeFromStats = false;
    protected ?string $invoiceDate = null;
    protected ?string $closingDate = null;
    protected int $workspaceId;
    protected string $createdAt;
    protected string $updatedAt;

protected array $toHidrate = [
        'uuid',
        'name',
        'type',
        'installement',
        'installment_value',
        'credit_limit',
        'currency',
        'balance',
        'exclude_from_stats',
        'invoice_date',
        'closing_date',
        'workspace_id',
        'created_at',
        'updated_at'
    ];

    public function __construct(EntryInterface|array $data)
    {
        if(is_array($data)) {
            $this->hydrateFromElastic($data);
        } else {
            $this->hydrate($data);
        }
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function fromElasticsearchResult(array $data): self
    {
        return new self($data);
    }

    public function hydrateFromElastic(array $data): self
    {
        foreach ($this->toHidrate as $key) {
            if (array_key_exists($key, $data)) {
                $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                if (method_exists($this, $setter)) {
                    $this->$setter($data[$key]);
                }
            }
        }
        return $this;
    }

    public function hydrate(EntryInterface $data): self
    {
        foreach ($this->toHidrate as $key) {
            $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $setter)) {
                $this->$setter($data->$key);
            }
        }
        return $this;
    }

    public function toArray() : array
    {
        $result = [];
        foreach ($this->toHidrate as $key) {
            $getter = 'get' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $getter)) {
                $result[$key] = $this->$getter();
            }
        }
        return $result;
        
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function mapping(): array
    {
        return [
            'uuid' => ['type' => 'keyword'],
            'name' => ['type' => 'text'],
            'type' => ['type' => 'keyword'],
            'installement' => ['type' => 'integer'],
            'installment_value' => ['type' => 'float'],
            'credit_limit' => ['type' => 'float'],
            'currency' => ['type' => 'keyword'],
            'balance' => ['type' => 'float'],
            'exclude_from_stats' => ['type' => 'boolean'],
            'invoice_date' => ['type' => 'date'],
            'closing_date' => ['type' => 'date'],
            'workspace_id' => ['type' => 'keyword'],
            'created_at' => ['type' => 'date'],
            'updated_at' => ['type' => 'date']
        ];
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getInstallement(): ?int
    {
        return $this->installement;
    }

    public function setInstallement(?int $installement): self
    {
        $this->installement = $installement;
        return $this;
    }

    public function getInstallmentValue(): ?float
    {
        return $this->installmentValue;
    }

    public function setInstallmentValue(?float $installmentValue): self
    {
        $this->installmentValue = $installmentValue;
        return $this;
    }

    public function getCreditLimit(): ?float
    {
        return $this->creditLimit;
    }

    public function setCreditLimit(?float $creditLimit): self
    {
        $this->creditLimit = $creditLimit;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function getExcludeFromStats(): bool
    {
        return $this->excludeFromStats;
    }

    public function setExcludeFromStats(bool $excludeFromStats): self
    {
        $this->excludeFromStats = $excludeFromStats;
        return $this;
    }

    public function getInvoiceDate(): ?string
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(?string $invoiceDate): self
    {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    public function getClosingDate(): ?string
    {
        return $this->closingDate;
    }

    public function setClosingDate(?string $closingDate): self
    {
        $this->closingDate = $closingDate;
        return $this;
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function setWorkspaceId(int $workspaceId): self
    {
        $this->workspaceId = $workspaceId;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}