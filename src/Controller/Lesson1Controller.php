<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/first")
 */
class Lesson1Controller extends AbstractController {

    /**
     * @Route (path="/demo/{id}/{lang}", name="demoRoute", requirements={ "id": "\d+" })
     * @param Request $request
     * @param int $id
     * @param string $lang
     * @return Response
     */
    public function MyFirstRequest(Request $request, int $id, string $lang="hu"){
        $str = "Hello, Symfony! - ";
        $str .= "ID = {$id} - ";
        $str .= "LANG = {$lang}";
        return new Response($str);
    }
}