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

    /**
     * @Route(path="list", name="getListAction")
     */
    public function getList(){
        
        $str = $this->getListHtml();
        return new Response($str);
    }

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

    private function queryHighestSalary(){
        $db = $this->readDatabase();
        $salaries = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 6) {
                    array_push($salaries, $value);
                }
                $i++;
            }
        }

        $idx = array_search(max($salaries), $salaries);
        return $idx;
    }

    private function getTable(){
        $database = $this->readDatabase();
        $tpl_table = file_get_contents("../templates/tsv/table.html");
        $tpl_rowsep = file_get_contents("../templates/tsv/rowsep.html");
        $tpl_cell = file_get_contents("../templates/tsv/cell.html");

        $rows = "";

        foreach ($database as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                $rows .= str_replace("{{ CELL }}", $value, $tpl_cell);
            
                if ($i % 10 == 9) {
                    $rows .= $tpl_rowsep;
                }

                $i++;
            }
        }

        $tpl_table = str_replace("{{ ROWS }}", $rows, $tpl_table);

        return $tpl_table;
    }

    private function getListHtml(){
        $str = $this->getTable();
        $tp_list = file_get_contents("../templates/tsv/list.html");
        $tp_list = str_replace("{{ TABLE }}", $str, $tp_list);

        // Q1
        $max_sal_val = max($this->getSalaries());
        $max_idx = array_search($max_sal_val, $this->getSalaries());
        $max_sal_name = $this->getNames()[$max_idx];
        $tp_list = str_replace("{{ Q1 }}", $max_sal_name.", ".$max_sal_val, $tp_list);

        //Q2
        $ages = $this->getAges();
        $tp_list = str_replace("{{ Q2 }}", min($ages), $tp_list);

        //Q3
        $ages = $this->getAges();
        $average = array_sum($ages)/count(array_filter($ages));
        $tp_list = str_replace("{{ Q3 }}", $average, $tp_list);

        //Q4
        $positions = $this->getPositions();
        $values = array_count_values($positions);
        arsort($values);
        $popular = array_slice(array_keys($values), 0, 5, true);
        $tp_list = str_replace("{{ Q4 }}", $popular[0], $tp_list);

        //Q5
        $genders = $this->getGenders();
        $total_count = count($genders);
        $male_count = 0;
        $female_count = 0;
        foreach ($genders as $key => $value) {
            if ($value == 'male') {
                $male_count++;
            }
            else {
                $female_count++;
            }
        }

        $tp_list = str_replace("{{ Q5A }}", $total_count, $tp_list);
        $tp_list = str_replace("{{ Q5B }}", ($male_count/$total_count) * 100, $tp_list);
        $tp_list = str_replace("{{ Q5C }}", ($female_count/$total_count) * 100, $tp_list);

        return $tp_list;
    }

    private function getNames(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 0) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getGenders(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 1) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getDates(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 2) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getAges(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 3) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getLevels(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 4) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getPositions(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 5) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getSalaries(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 6) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getAddresses(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 7) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getEmails(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 8) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }

    private function getPhones(){
        $db = $this->readDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 9) {
                    array_push($array, $value);
                }
                $i++;
            }
        }

        return $array;
    }
}