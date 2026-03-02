<?php
declare(strict_types=1);

namespace BudgetcontrolLibs\ElasticSearch\Entities\Transactions;

use Budgetcontrol\Library\Model\Currency;
use Budgetcontrol\Library\Model\Entry;
use Budgetcontrol\Library\Model\EntryInterface;
use Budgetcontrol\Library\Model\Payee;
use Budgetcontrol\Library\Model\PaymentType;
use Budgetcontrol\Library\Model\SubCategory;
use Budgetcontrol\Library\Model\Wallet;
use BudgetcontrolLibs\ElasticSearch\Entities\TransactionInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use JsonSerializable;

class EntryTransaction implements JsonSerializable, TransactionInterface
{
    private string $uuid;
    private ?string $note = null;
    private int $workspace_id;
    private float $amount;
    private string $type;
    private string $payment_type;
    private string $currency;
    private int $category_id;
    private string $category_name;
    private string $category_slug;
    private int $wallet_id;
    private Wallet $wallet;
    private string $date;
    private int $timestamp;
    private int $year;
    private int $month;
    private int $day;
    private int $day_of_week;
    private int $week_of_year;
    private int $quarter;
    private ?array $labels = null;
    private bool $have_payee;
    private ?Payee $payee = null;
    private bool $confirmed;
    private bool $planned;
    private bool $have_warranty;
    private bool $is_transfer;
    private ?string $transfer_relation = null;
    private mixed $geolocalization = null;
    private string $created_at;
    private string $updated_at;

    protected array $toHidrate = [
        'uuid',
        'note',
        'workspace_id',
        'amount',
        'type',
        'paymentType',
        'currency',
        'category_id',
        'category',
        'account_id',
        'subCategory',
        'wallet',
        'date_time',
        'labels',
        'payee_id',
        'payee',
        'confirmed',
        'planned',
        'warranty',
        'transfer',
        'transfer_relation',
        'geolocalization',
        'created_at',
        'updated_at'
    ];

    public function __construct(Entry|array $data)
    {
        if(is_array($data)) {
            $this->hydrateFromElastic($data);
        } else {
            $this->hydrate($data);
        }
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

    protected function hydrateFromElastic(array $data): self
    {
        foreach ($data as  $key => $value) {
            $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $setter)) {
                $this->$setter($data->$key);
            }
        }
        return $this;
    }

    // Getters
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getWorkspaceId(): int
    {
        return $this->workspace_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPaymentType(): ?int
    {
        return $this->payment_type;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    public function getWalletId(): int
    {
        return $this->wallet_id;
    }

    public function getWallet(): array
    {
        return $this->wallet;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getDayOfWeek(): int
    {
        return $this->day_of_week;
    }

    public function getWeekOfYear(): int
    {
        return $this->week_of_year;
    }

    public function getQuarter(): int
    {
        return $this->quarter;
    }

    public function getLabels(): ?array
    {
        return $this->labels;
    }

    public function getHavePayee(): bool
    {
        return $this->have_payee;
    }

    public function getPayee(): ?array
    {
        return $this->payee;
    }

    public function getConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function getPlanned(): bool
    {
        return $this->planned;
    }

    public function getHaveWarranty(): bool
    {
        return $this->have_warranty;
    }

    public function getIsTransfer(): bool
    {
        return $this->is_transfer;
    }

    public function getTransferRelation(): ?string
    {
        return $this->transfer_relation;
    }

    public function getGeolocalization()
    {
        return $this->geolocalization;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    // Setters
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function setWorkspaceId(int $workspace_id): self
    {
        $this->workspace_id = $workspace_id;
        return $this;
    }

    public function setAmount(float|string $amount): self
    {
        $this->amount = (float) $amount;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setPaymentType(PaymentType $payment_type): self
    {
        $this->payment_type = $payment_type->name;
        return $this;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency->icon;
        return $this;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function setSubCategory(SubCategory $category): self
    {
        $this->category_name = $category->name;
        $this->category_slug = $category->slug;
        return $this;
    }

    //FIXME: account is the old name of wallet, we should rename it everywhere, but for now we need to keep both for compatibility with old data and code
    public function setAccountId(int $wallet_id): self
    {
        $this->wallet_id = $wallet_id;
        return $this;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;
        return $this;
    }

    public function setDateTime($date): self
    {
        $this->date = $date;
        // format date example: "2025-09-18T12:08:07+00:00"
        $carbon = Carbon::parse($date);
        $this->setTimestamp($carbon->getTimestamp());
        $this->setYear((int) $carbon->format('Y'));
        $this->setMonth((int) $carbon->format('m'));
        $this->setDay((int) $carbon->format('d'));
        $this->setDayOfWeek((int) $carbon->format('N'));
        $this->setWeekOfYear((int) $carbon->format('W'));
        $this->setQuarter($carbon->quarter);

        return $this;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;
        return $this;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;
        return $this;
    }

    public function setDayOfWeek(int $day_of_week): self
    {
        $this->day_of_week = $day_of_week;
        return $this;
    }

    public function setWeekOfYear(int $week_of_year): self
    {
        $this->week_of_year = $week_of_year;
        return $this;
    }

    public function setQuarter(int $quarter): self
    {
        $this->quarter = $quarter;
        return $this;
    }

    public function setLabels(?Collection $labels): self
    {
        if($labels->count() === 0) {
            $this->labels = null;
            return $this;
        }

        $this->labels = $labels->toArray();
        return $this;
    }

    public function setPayeeId(?int $have_payee): self
    {   
        if($have_payee === null) {
            $this->have_payee = false;
            return $this;
        }

        $this->have_payee = true;
        return $this;
    }

    public function setPayee(?Payee $payee): self
    {
        $this->payee = $payee;
        return $this;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function setPlanned(bool $planned): self
    {
        $this->planned = $planned;
        return $this;
    }

    public function setWarranty(bool $have_warranty): self
    {
        $this->have_warranty = $have_warranty;
        return $this;
    }

    public function setTransfer(bool $is_transfer): self
    {
        $this->is_transfer = $is_transfer;
        return $this;
    }

    public function setTransferRelation(?string $transfer_relation): self
    {
        $this->transfer_relation = $transfer_relation;
        return $this;
    }

    public function setGeolocalization($geolocalization): self
    {
        $this->geolocalization = $geolocalization;
        return $this;
    }

    public function setCreatedAt($created_at): self
    {
        if ($created_at instanceof Carbon) {
            $this->created_at = $created_at->toISOString();
        } elseif (is_string($created_at)) {
            $this->created_at = $created_at;
        }
        return $this;
    }

    public function setUpdatedAt($updated_at): self
    {
        if ($updated_at instanceof Carbon) {
            $this->updated_at = $updated_at->toISOString();
        } elseif (is_string($updated_at)) {
            $this->updated_at = $updated_at;
        }
        return $this;
    }

    // Utility methods
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'note' => $this->note,
            'workspace_id' => $this->workspace_id,
            'amount' => $this->amount,
            'type' => $this->type,
            'payment_type' => $this->payment_type,
            'currency' => $this->currency,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_slug' => $this->category_slug,
            'wallet_id' => $this->wallet_id,
            'wallet' => $this->wallet->toArray(),
            'date' => $this->date,
            'timestamp' => $this->timestamp,
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
            'day_of_week' => $this->day_of_week,
            'week_of_year' => $this->week_of_year,
            'quarter' => $this->quarter,
            'labels' => $this->labels,
            'have_payee' => $this->have_payee,
            'payee' => $this->payee?->toArray(),
            'confirmed' => $this->confirmed,
            'planned' => $this->planned,
            'have_warranty' => $this->have_warranty,
            'is_transfer' => $this->is_transfer,
            'transfer_relation' => $this->transfer_relation,
            'geolocalization' => $this->geolocalization,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public static function fromElasticsearchResult(array $hit): self
    {
        return new self($hit);
    }

    /**
     * Get Elasticsearch document ID (uses uuid)
     */
    public function getDocumentId(): ?string
    {
        return $this->uuid;
    }

    /**
     * Get only non-null fields for partial updates
     */
    public function getUpdatedFields(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null);
    }

    /**
     * Check if transaction is valid for indexing
     */
    public function isValid(): bool
    {
        return !empty($this->uuid) && $this->amount !== null && !empty($this->date);
    }

    /**
     * Update date fields based on a Carbon instance
     */
    public function updateDateFields(Carbon $carbon): self
    {
        $this->date = $carbon->format('Y-m-d H:i:s');
        $this->timestamp = $carbon->getTimestamp();
        $this->year = (int) $carbon->format('Y');
        $this->month = (int) $carbon->format('m');
        $this->day = (int) $carbon->format('d');
        $this->day_of_week = (int) $carbon->format('N');
        $this->week_of_year = (int) $carbon->format('W');
        $this->quarter = $carbon->quarter;
        return $this;
    }

    public static function mapping(): array
    {
        return [
            'properties' => [
                'uuid' => ['type' => 'keyword'],
                'note' => [
                    'type' => 'text',
                    'analyzer' => 'my_analyzer',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
                'workspace_id' => ['type' => 'integer'],
                'amount' => ['type' => 'float'],
                'type' => ['type' => 'keyword'],
                'payment_type' => ['type' => 'keyword'],
                'currency' => ['type' => 'keyword'],
                'category_id' => ['type' => 'integer'],
                'category_name' => ['type' => 'keyword'],
                'category_slug' => ['type' => 'keyword'],
                'wallet_id' => ['type' => 'integer'],
                'wallet' => [
                    'type' => 'object',
                    'enabled' => true
                ],
                'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||strict_date_optional_time||epoch_millis||yyyy-MM-dd||yyyy-MM-dd\'T\'HH:mm:ssZ'],
                'timestamp' => ['type' => 'long'],
                'year' => ['type' => 'integer'],
                'month' => ['type' => 'integer'],
                'day' => ['type' => 'integer'],
                'day_of_week' => ['type' => 'integer'],
                'week_of_year' => ['type' => 'integer'],
                'quarter' => ['type' => 'integer'],
                'labels' => [
                    'type' => 'object',
                    'enabled' => true
                ],
                'have_payee' => ['type' => 'boolean'],
                'payee' => [
                    'type' => 'object',
                    'enabled' => true
                ],
                'confirmed' => ['type' => 'boolean'],
                'planned' => ['type' => 'boolean'],
                'have_warranty' => ['type' => 'boolean'],
                'is_transfer' => ['type' => 'boolean'],
                'transfer_relation' => ['type' => 'keyword'],
                'geolocalization' => ['type' => 'geo_point'],
                'created_at' => ['type' => 'date'],
                'updated_at' => ['type' => 'date'],
            ],
        ];
    }
}
