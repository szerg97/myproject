<?php


namespace App\Service;


use App\Entity\Car;
use Symfony\Component\Form\FormInterface;

interface ICarService
{
    /**
     * @return Car[]|iterable
     */
    public function getAllCars() : iterable;

    /**
     * @param int $brandId
     * @return Car[]|iterable
     */
    public function getCarsByBrand(int $brandId): iterable;

    /**
     * @param bool $isVisible
     * @return Car[]|iterable
     */
    public function getCarsByVisibility(bool $isVisible): iterable;

    public function getCarById(int $carId): Car;

    public function saveCar(Car $car) : void;

    public function removeCar(int $carId) : void;

    public function getCarForm(Car $oneCar) : FormInterface;
}