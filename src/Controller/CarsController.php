<?php

namespace App\Controller;

use App\Entity\Car;
use App\Service\ICarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CarsController
 * @package App\Controller
 * @Route(path="/cars/")
 */
class CarsController extends AbstractController
{
    /** @var ICarService  */
    private $carService;

    public function __construct(ICarService $carService){
        $this->carService = $carService;
    }
    // routes: carlist(brandId?), carshow(carId), cardel(carId), caredit(carId?)

    // /cars/list
    // /cars/list/5
    // /cars/list?isVisible=false
    /**
     * @param Request $request
     * @param int $brandId
     * @return Response
     * @Route(name="carlist", path="list/{brandId}", requirements={ "brandId" : "\d+"})
     */
    public function listAction(Request $request, $brandId = 0): Response{
        //TODO convert db entity into a form DTO
        if ($brandId){
            $cars = $this->carService->getCarsByBrand($brandId);
        }
        else{
            $isVisible = $request->query->getBoolean("isVisible");
            if ($isVisible == null){
                $cars = $this->carService->getAllCars();
            }
            else{
                $cars = $this->carService->getCarsByVisibility($isVisible);
            }
        }

        return $this->render("cars/carlist.html.twig", ["cars" => $cars]);
    }

    /**
     * @param Request $request
     * @param int $carId
     * @return Response
     * @Route(name="carshow", path="show/{carId}", requirements={ "carId" : "\d+"})
     */
    public function showAction(Request $request, int $carId): Response{
        //TODO convert db entity into a form DTO
        $oneCar = $this->carService->getCarById($carId);
        return $this->render("cars/carshow.html.twig", ["car" => $oneCar]);
    }

    /**
     * @param Request $request
     * @param int $carId
     * @return Response
     * @Route(name="cardel", path="del/{carId}", requirements={ "carId" : "\d+"})
     */
    public function delAction(Request $request, int $carId): Response{
        $this->carService->removeCar($carId);
        $this->addFlash('notice', 'CAR REMOVED');
        return $this->redirectToRoute('carlist');
    }

    /**
     * @param Request $request
     * @param int $carId
     * @return Response
     * @Route(name="caredit", path="edit/{carId}", requirements={ "carId" : "\d+"})
     */
    public function editAction(Request $request, int $carId = 0): Response{
        //TODO convert db entity into a form DTO
        if ($carId){
            $oneCar = $this->carService->getCarById($carId);
        }
        else{
            $oneCar = new Car();
        }

        $form = $this->carService->getCarForm($oneCar);
        $form->handleRequest($request); //$_POST => $oneCar
        if($form->isSubmitted() && $form->isValid()){
            $this->carService->saveCar($oneCar);
            $this->addFlash("notice", "CAR SAVED");
            return $this->redirectToRoute("carlist");
        }

        return $this->render("cars/caredit.html.twig", ["form" => $form->createView()]);
    }
}