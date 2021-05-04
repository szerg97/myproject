<?php


namespace App\Service;


use App\Entity\Brand;
use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarService extends CrudService implements ICarService
{
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository(Car::class);
    }

    public function getAllCars(): iterable
    {
        return $this->getRepository()->findAll();
    }

    public function getCarsByBrand(int $brandId): iterable
    {
        return $this->getRepository()->findBy(["car_brand" => $brandId]);
    }

    public function getCarsByVisibility(bool $isVisible): iterable
    {
        //return $this->getRepository()->findBy(["car_visible" => $isVisible]);
        $qb = $this->em->createQueryBuilder();
        $qb->select("car")
            ->from(Car::class, "car")
            // ->innerJoin(Brand::class, "brand")
            ->where("car.car_visible = :visible")
            ->orderBy("car.car_price", "desc")
            ->setParameter("visible", $isVisible);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getCarById(int $carId): Car
    {
        /** @var Car|null $oneCar */
        $oneCar = $this->getRepository()->find($carId);
        if ($oneCar == null){
            throw new NotFoundHttpException("NO CAR FOUND");
        }
        return $oneCar;
    }

    public function saveCar(Car $car): void
    {
        $this->em->persist($car);
        $this->em->flush();
    }

    public function removeCar(int $carId): void
    {
        $oneCar = $this->getCarById($carId);
        if ($oneCar == null){
            throw new NotFoundHttpException("NO CAR FOUND");
        }
        $this->em->remove($oneCar);
        $this->em->flush();
    }

    public function getCarForm(Car $oneCar): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $oneCar);
        $form->add("car_model", TextType::class, ["required" => false]);
        $form->add("car_price", NumberType::class, ["required" => false]);
        $form->add("car_visible", ChoiceType::class, [
            "choices" => ["YES" => true, "NO" => false]
        ]);
        $form->add("car_brand", EntityType::class, [
            "class" => Brand::class,
            "choice_label" => "brand_name", //displayed to the user
            "choice_value" => "brand_id" //PK value saved in the db
        ]);
        $form->add("SAVE", SubmitType::class);
        return $form->getForm();
    }


}