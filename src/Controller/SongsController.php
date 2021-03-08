<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SongsController
 * @package App\Controller
 * @Route(path="songs/")
 */
class SongsController{

    
    private function getSelectSongs(): string {
        $songs_array = $this->getSongs();
        $tpl_form = file_get_contents("../templates/songs/form.html");
        $tpl_option = file_get_contents("../templates/songs/option.html");

        $rows = "";

        for ($i=0; $i < count($songs_array); $i++) {
            $str = $songs_array[$i];
            $rows .= str_replace("{{ OPTION }}", "$str", "$tpl_option");
        }

        $output = $tpl_form;
        $output = str_replace("{{ OPTIONS }}", $rows, $output);

        return $output;
    }

    private function getSongs(): array {
        $lines = file("../templates/songs/songs.txt");
        return $lines;
    }

    private function getTable() : string{
        $songs_votes_array = $this->getVotesArray();
        $tpl_list = file_get_contents("../templates/songs/list.html");
        $tpl_row = file_get_contents("../templates/songs/row.html");

        $rows = "";

        foreach ($songs_votes_array as $key => $value) {
            $str1 = "{$key} ({$value})";
            $rows .= str_replace("{{ SONG }}", "$str1", "$tpl_row");
        }

        $output = $tpl_list;
        $output = str_replace("{{ rows }}", $rows, $output);

        return $output;
    }

    private function getVotes(): array{
        $lines = file("../templates/songs/songvotes.txt");
        return $lines;
    }

    private function getVotesArray(): array{
        $songs_array = $this->getSongs();
        $votes_array = $this->getVotes();
        $songs_votes_array = array();


        for ($i=0; $i < count($songs_array); $i++) { 
            $count = 0;

            for ($j=0; $j < count($votes_array); $j++) { 
                if ($songs_array[$i] == $votes_array[$j]) {
                    $count++;
                }
            }

            $songs_votes_array["$songs_array[$i]"] = $count;
        }        

        return $songs_votes_array;
    }

    private function getVoters(): array{
        $lines = $this->getVotes();
        $voters_array = array();

        $count = 0;

        for ($i=0; $i < count($lines); $i++) { 
            if ($count % 4 == 0) {
                array_push($voters_array, $lines[$i]);
                array_push($voters_array, $lines[$i+1]);
            }
            $count++;
        }

        file_put_contents("../templates/songs/voters.txt", $voters_array);
        return $voters_array;
    }

    private function getWinner(){
        $voters_array = $this->getVoters();
        $rndNum = rand(0, count($voters_array)-1);
        if ($rndNum % 2 == 0) {
            $winner = $voters_array[$rndNum];
            $email = $voters_array[$rndNum + 1];
        }
        else{
            $winner = $voters_array[$rndNum-1];
            $email = $voters_array[$rndNum];
        }

        $tpl_lottery = file_get_contents("../templates/songs/lottery.html");
        $tpl_lottery = str_replace("{{ PERSON }}", $winner, $tpl_lottery);
        $tpl_lottery = str_replace("{{ EMAIL }}", $email, $tpl_lottery);

        $output = $tpl_lottery;

        return $output;
    }
    

    /**
     * @param Request $request
     * @Route(path="vote", name="getVoteAction")
     * @return Response
     */
    public function getVote(Request $request){
        $name = $request->request->get("name");
        $email = $request->request->get("email");
        $song = $request->request->get("song");

        if ($name != null && $email != null && $song != null) {
            file_put_contents("../templates/songs/songvotes.txt", "$name\n", FILE_APPEND);
            file_put_contents("../templates/songs/songvotes.txt", "$email\n", FILE_APPEND);
            file_put_contents("../templates/songs/songvotes.txt", "$song\n", FILE_APPEND);
        }

        return new RedirectResponse('list');
    }

    /**
     * @param Request $request
     * @Route(path="lottery", name="getLotteryAction")
     * @return Response
     */
    public function getLottery(Request $request): Response{

        $str = $this->getWinner();

        return new Response($str);
    }

    /**
     * @param Request $request
     * @Route(path="form", name="getFormAction")
     * @return Response
     */
    public function getForm(Request $request): Response{
        $str = $this->getSelectSongs();
        
        return new Response($str);
    }

    /**
     * @param Request $request
     * @Route(path="list", name="getListAction")
     * @return Response
     */
    public function getList(Request $request) : Response{
        $str = $this->getTable();

        return new Response($str);
    }
}