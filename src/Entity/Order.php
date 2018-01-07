<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Litipk\BigNumbers\Decimal;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Cache(usage="READ_ONLY")
 */
class Order implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="bigint", nullable=false, options={"unsigned"=true,"comment"="Facebook ID"})
     * @see https://developers.facebook.com/docs/graph-api/reference/user/
     */
    private $facebookId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime", nullable=false, options={"comment"="Дата создания записи"}, columnDefinition="DATETIME DEFAULT NOW() NOT NULL")
     */
    private $dateCreate;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Валюта. Справочник ISO 4217"})
     */
    private $currency;

    /**
     * @var Decimal
     *
     * @ORM\Column(name="total_cost", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Общая стоимость заказа"})
     */
    private $totalCost;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_legal_person", type="boolean", nullable=false, options={"comment"="Юридическое лицо"}, columnDefinition="TINYINT(1) DEFAULT '0'")
     */
    private $isLegalPerson = false;

    /**
     * @var array|null
     *
     * @ORM\Column(name="attributes", type="json", nullable=true, options={"comment"="Произвольные атрибуты в JSON"})
     */
    private $attributes;

    public function __construct()
    {
        $this->dateCreate = new \DateTime();
    }

    public function jsonSerialize()
    {
        return \get_object_vars($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Order
     */
    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookId(): string
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     * @return Order
     */
    public function setFacebookId(string $facebookId): Order
    {
        $this->facebookId = $facebookId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreate(): \DateTime
    {
        return $this->dateCreate;
    }

    /**
     * @param \DateTime $dateCreate
     * @return Order
     */
    public function setDateCreate(\DateTime $dateCreate): Order
    {
        $this->dateCreate = $dateCreate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Order
     */
    public function setCurrency(string $currency): Order
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param mixed $value
     * @return Decimal
     * @throws \TypeError
     */
    protected static function makeTotalCost($value)
    {
        if (\is_string($value)) {
            $value = \str_replace(',', '.', $value);
        }
        return Decimal::create($value, 2);
    }

    /**
     * @return Decimal
     * @throws \TypeError
     */
    public function getTotalCost(): Decimal
    {
        return static::makeTotalCost($this->totalCost);
    }

    /**
     * @param Decimal|string $totalCost
     * @return Order
     * @throws \TypeError
     */
    public function setTotalCost($totalCost): Order
    {
        if (!($totalCost instanceof Decimal)) {
            $totalCost = static::makeTotalCost($totalCost);
        }
        $this->totalCost = $totalCost;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLegalPerson(): bool
    {
        return $this->isLegalPerson;
    }

    /**
     * @param bool $isLegalPerson
     * @return Order
     */
    public function setIsLegalPerson(bool $isLegalPerson): Order
    {
        $this->isLegalPerson = $isLegalPerson;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param array|null $attributes
     * @return Order
     */
    public function setAttributes(?array $attributes): Order
    {
        $this->attributes = $attributes;
        return $this;
    }
}
