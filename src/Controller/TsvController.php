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

    const PATH_ORIGINAL = "../templates/tsv/employees.csv";
    const PATH_NEW = "../templates/tsv/employees_new.csv";

    private function readOriginalDatabase(): array{
        $database = array();
        $rows_array = file(self::PATH_ORIGINAL, FILE_IGNORE_NEW_LINES);
        foreach ($rows_array as $key => $row) {
            $database[] = explode(";", $row);
        }

        return $database;
    }

    private function readNewDatabase(): array{
        $database = array();
        $rows_array = file(self::PATH_NEW, FILE_IGNORE_NEW_LINES);
        foreach ($rows_array as $key => $row) {
            $database[] = explode(";", $row);
        }

        return $database;
    }

    private function writeNewDatabase(array $database){
        $cont = "";
        foreach ($database as $one_record) {
            $cont .= implode(";", $one_record)."\n";
        }

        file_put_contents(self::PATH_NEW, $cont);
    }

    private function generateRandomEntities(): array{
        $original_dataset = $this->readOriginalDatabase();
        $original_records = array();
        $new_dataset = array();
        $new_records = array();

        foreach ($original_dataset as $key => $rec) {
            array_push($original_records, $rec);
        }

        for ($i=0; $i < 20; $i++) {
             for ($j=0; $j < 9; $j++) { 
                $new_records[$i][$j] = $original_dataset[rand(0, 9)][$j];
             }
        }

        for ($i=0; $i < 20; $i++) { 
            $new_dataset[$i] = $new_records[$i];
        }

        $this->writeNewDatabase($new_dataset);
        return $new_dataset;
    }

    private function getTableNew(){
        $database = $this->generateRandomEntities();
        $tpl_table = file_get_contents("../templates/tsv/table.html");
        $tpl_rowsep = file_get_contents("../templates/tsv/rowsep.html");
        $tpl_cell = file_get_contents("../templates/tsv/cell.html");

        $rows = "";

        foreach ($database as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                $rows .= str_replace("{{ CELL }}", $value, $tpl_cell);
            
                if ($i % 10 == 8) {
                    $rows .= $tpl_rowsep;
                }

                $i++;
            }
        }

        $tpl_table = str_replace("{{ ROWS }}", $rows, $tpl_table);

        return $tpl_table;
    }

    private function getTable(){
        $database = $this->readOriginalDatabase();
        $tpl_table = file_get_contents("../templates/tsv/table.html");
        $tpl_rowsep = file_get_contents("../templates/tsv/rowsep.html");
        $tpl_cell = file_get_contents("../templates/tsv/cell.html");

        $rows = "";

        foreach ($database as $key => $rec) {
            foreach ($rec as $key2 => $value) {
                $rows .= str_replace("{{ CELL }}", $value, $tpl_cell);
            }
            $rows .= $tpl_rowsep;
        }

        $tpl_table = str_replace("{{ ROWS }}", $rows, $tpl_table);

        return $tpl_table;
    }

    /**
     * @Route(path="list", name="getListAction")
     */
    public function getList(): Response{
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
        $tp_list = str_replace("{{ Q5B }}", round(($male_count/$total_count) * 100, 2), $tp_list);
        $tp_list = str_replace("{{ Q5C }}", round(($female_count/$total_count) * 100, 2), $tp_list);

        //DISPLAY NEW TABLE

        $str2 = $this->getTableNew();
        $tp_list = str_replace("{{ TABLE_NEW }}", $str2, $tp_list);

        //Q6
        $db_original = $this->readOriginalDatabase();
        $db_new = $this->readNewDatabase();

        $orig_dates = $this->getDates();
        $new_dates = $this->getDatesNew();
        $merged_all = array_merge($orig_dates,$new_dates);

        $res = array_diff($merged_all, $new_dates);

        $str_q6 = "";
        foreach ($res as $key => $value) {
            $str_q6 .= " | ".$value;
        }

        $tp_list = str_replace("{{ Q6 }}", count($res).$str_q6, $tp_list);

        //Q7
        $positions = $this->getPositions();
        $positions_new = $this->getPositionsNew();
        $merged = array_merge($positions,$positions_new);
        $total_count = count($merged);
        $it_count = 0;
        $sales_count = 0;
        $manage_count = 0;
        $finance_count = 0;
        foreach ($merged as $key => $value) {
            if ($value == 'IT') {
                $it_count++;
            }
            else if ($value == 'Sales'){
                $sales_count++;
            }
            else if ($value == 'Management'){
                $manage_count++;
            }
            else {
                $finance_count++;
            }
        }
        $tp_list = str_replace("{{ Q7A }}", $total_count, $tp_list);
        $tp_list = str_replace("{{ Q7B }}", round(($it_count/$total_count) * 100, 2), $tp_list);
        $tp_list = str_replace("{{ Q7C }}", round(($sales_count/$total_count) * 100, 2), $tp_list);
        $tp_list = str_replace("{{ Q7D }}", round(($manage_count/$total_count) * 100, 2), $tp_list);
        $tp_list = str_replace("{{ Q7E }}", round(($finance_count/$total_count) * 100, 2), $tp_list);

        //Q8
        $db_original = $this->readOriginalDatabase();
        $db_new = $this->readNewDatabase();
        $merged = array_merge($db_original,$db_new);
        $sals_array = array();

        foreach ($merged as $key => $rec) {
            if ($rec[4] == 'Sales') {
                array_push($sals_array, $rec[5]);
            }
        }

        $tp_list = str_replace("{{ Q8 }}", array_sum($sals_array), $tp_list);

        return new Response($tp_list);
    }

    private function getNames(){
        $db = $this->readOriginalDatabase();
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
        $db = $this->readOriginalDatabase();
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

    private function getGendersNew(){
        $db = $this->readNewDatabase();
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
        $db = $this->readOriginalDatabase();
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

    private function getDatesNew(){
        $db = $this->readNewDatabase();
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
        $db = $this->readOriginalDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 2) {
                    array_push($array, 2021 - substr($value, 0, -6));
                }
                $i++;
            }
        }

        return $array;

    }

    private function getAgesNew(){
        $db = $this->readNewDatabase();
        $array = array();

        foreach ($db as $key => $rec) {
            $i = 0;
            foreach ($rec as $key2 => $value) {
                if ($i == 2) {
                    array_push($array, 2021 - substr($value, 0, -6));
                }
                $i++;
            }
        }

        return $array;

    }

    private function getLevels(){
        $db = $this->readOriginalDatabase();
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

    private function getPositions(){
        $db = $this->readOriginalDatabase();
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

    private function getPositionsNew(){
        $db = $this->readNewDatabase();
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

    private function getSalaries(){
        $db = $this->readOriginalDatabase();
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

    private function getSalariesNew(){
        $db = $this->readNewDatabase();
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

    private function getAddresses(){
        $db = $this->readOriginalDatabase();
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

    private function getEmails(){
        $db = $this->readOriginalDatabase();
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

    private function getPhones(){
        $db = $this->readOriginalDatabase();
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
}