<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TsvController
 * @package App\Controller
 * @Route(path="tsv/")
 */
class TsvController{

    const PATH = "../templates/tsv/employees.csv";

    private function readDatabase(): array{
        $database = array();
        $rows_array = file(self::PATH, FILE_IGNORE_NEW_LINES);
        foreach ($rows_array as $key => $row) {
            $database[] = explode(";", $row);
        }

        return $database;
    }

    private function writeDatabase(array $database){
        $cont = "";
        foreach ($database as $one_record) {
            $cont .= implode(";", $one_record)."\n";
        }

        file_put_contents(self::PATH, $cont);
    }

    private function getTable(){
        $database = $this->readDatabase();
        $tpl_list = file_get_contents("../templates/tsv/list.html");
        $tpl_rowsep = file_get_contents("../templates/tsv/rowsep.html");
        $tpl_cell = file_get_contents("../templates/tsv/cell.html");

        $rows = "";

        foreach ($database as $key => $value) {
            $i = 0;
            foreach ($value as $key2 => $value2) {
                $rows .= str_replace("{{ CELL }}", $value2, $tpl_cell);
            
                if ($i % 10 == 9) {
                    $rows .= $tpl_rowsep;
                }

                $i++;
            }
        }

        $tpl_list = str_replace("{{ ROWS }}", $rows, $tpl_list);

        return $tpl_list;
    }

    /**
     * @Route(path="list", name="getListAction")
     */
    public function getList(){
        
        $str = $this->getTable();
        return new Response($str);
    }
}