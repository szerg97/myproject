<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Brand
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="brands")
 */
class Brand
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $brand_id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $brand_name;

    /**
     * @var ArrayCollection|null
     * @ORM\OneToMany(targetEntity="Car", mappedBy="car_brand")
     */
    private $brand_cars;

    public function __construct() {
        $this->brand_cars = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->brand_name;
    }

    /**
     * @return int
     */
    public function getBrandId(): int
    {
        return $this->brand_id;
    }

    /**
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brand_name;
    }

    /**
     * @param string|null $brand_name
     */
    public function setBrandName(?string $brand_name): void
    {
        $this->brand_name = $brand_name;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getBrandCars(): ?ArrayCollection
    {
        return $this->brand_cars;
    }

    /**
     * @param ArrayCollection|null $brand_cars
     */
    public function setBrandCars(?ArrayCollection $brand_cars): void
    {
        $this->brand_cars = $brand_cars;
    }

}