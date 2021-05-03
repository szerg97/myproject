<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Car
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="cars")
 * @ORM\HasLifecycleCallbacks
 */
class Car
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $car_id;

    /**
     * @var DateTime|null
     * @ORM\Column (type="datetime")
     */
    private $car_modified;

    /**
     * @var DateTime|null
     * @ORM\Column (type="datetime")
     */
    private $car_inserted;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $car_visible;

    /**
     * @var string|null
     * @ORM\Column (type="string", length=100, nullable=true)
     */
    private $car_model;

    /**
     * @var float|null
     * @ORM\Column (type="decimal", scale=2, precision=10, nullable=true)
     */
    private $car_price;

    /**
     * @var Brand|null
     * @ORM\JoinColumn(name="car_brand", referencedColumnName="brand_id")
     * @ORM\ManyToOne (targetEntity="Brand", inversedBy="brand_cars")
     */
    private $car_brand;

    public function __toString(): string
    {
        $brandName = $this->car_brand ? $this->car_brand->getBrandName() : "N.A.";
        return "{$brandName} | {$this->car_model}";
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void {
        $this->car_modified = new DateTime();
        if ($this->car_inserted == null) $this->car_inserted = new DateTime();
    }

    /**
     * @return int
     */
    public function getCarId(): int
    {
        return $this->car_id;
    }

    /**
     * @return DateTime|null
     */
    public function getCarModified(): ?DateTime
    {
        return $this->car_modified;
    }

    /**
     * @param DateTime|null $car_modified
     */
    public function setCarModified(?DateTime $car_modified): void
    {
        $this->car_modified = $car_modified;
    }

    /**
     * @return DateTime|null
     */
    public function getCarInserted(): ?DateTime
    {
        return $this->car_inserted;
    }

    /**
     * @param DateTime|null $car_inserted
     */
    public function setCarInserted(?DateTime $car_inserted): void
    {
        $this->car_inserted = $car_inserted;
    }

    /**
     * @return bool|null
     */
    public function getCarVisible(): ?bool
    {
        return $this->car_visible;
    }

    /**
     * @param bool|null $car_visible
     */
    public function setCarVisible(?bool $car_visible): void
    {
        $this->car_visible = $car_visible;
    }

    /**
     * @return string|null
     */
    public function getCarModel(): ?string
    {
        return $this->car_model;
    }

    /**
     * @param string|null $car_model
     */
    public function setCarModel(?string $car_model): void
    {
        $this->car_model = $car_model;
    }

    /**
     * @return float|null
     */
    public function getCarPrice(): ?float
    {
        return $this->car_price;
    }

    /**
     * @param float|null $car_price
     */
    public function setCarPrice(?float $car_price): void
    {
        $this->car_price = $car_price;
    }

    /**
     * @return Brand|null
     */
    public function getCarBrand(): ?Brand
    {
        return $this->car_brand;
    }

    /**
     * @param Brand|null $car_brand
     */
    public function setCarBrand(?Brand $car_brand): void
    {
        $this->car_brand = $car_brand;
    }


}