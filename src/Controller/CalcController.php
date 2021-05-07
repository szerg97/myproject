<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalcController{

    /**
     * @param Request $request
     * @return Response $response
     * @Route(path="/calc/form", name="calcFormRoute")
     */
    public function calcFormAction(Request $request) : Response{
        $html = file_get_contents("../templates/calc/calcform.html");
        return new Response($html);
    }

    /**
     * @param Request $request
     * @return Response $response
     * @Route(path="/calc/result", name="calcResultRoute")
     */
    public function calcResultAction(Request $request) : Response{
        // var_dump($request->request->all());
        // return new Response("Hello")

        $operand1 = $request->request->getInt("operand1");
        $operand2 = $request->request->getInt("operand2");
        $operator = $request->request->get("operator");
        $allowed_operators = array("+", "-", "*", "/");
        if (in_array($operator, $allowed_operators)) {
            $res = "";
            switch ($operator){
                case "+": $res = $operand1 + $operand2; break;
                case "-": $res = $operand1 - $operand2; break;
                case "*": $res = $operand1 * $operand2; break;
                case "/": 
                    if ($operand2 == 0){
                        $res = "ERROR";
                    }
                    else{
                        $res = $operand1 / $operand2;
                    }
                    break;
            }
            $output = "{$operand1} {$operator} {$operand2} = {$res}";
        }
        else{
            $output = "BAD OPERATOR";
        }

        $html = file_get_contents("../templates/calc/calcresult.html");
        $html= str_replace("{{ OUTPUT }}", "$output", "$html");

        return new Response($html);
    }
}