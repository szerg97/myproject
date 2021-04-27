<?php

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Question;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VotesController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes", name="votes_listq")
     */
    public function listqAction(Request $request): Response{
        //SHOULD NOT USE DOCTRINE IN CONTROLLER! USE IT IN SERVICE!
        //SHOULD USE A VIEWMODEL CLASS
        //SHOULD NOT SEND ENTITY TO VIEW/FORM
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render("votes/questions.html.twig", ["questions" => $questions]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/question/{question}", name="votes_listc", requirements={"question": "\d+"})
     */
    public function listcAction(Request $request, int $question): Response{
        /** @var Question $questionInstance */
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->find($question);
        if (!$questionInstance) throw $this->createNotFoundException();
        return $this->render("votes/choices.html.twig", ["choices" => $questionInstance->getQuChoices()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/votes/vote/{choice}", name="votes_vote", requirements={"choice": "\d+"})
     */
    public function voteAction(Request $request, int $choice): Response{
        /** @var Choice $choiceInstance */
        $choiceInstance = $this->getDoctrine()->getRepository(Choice::class)->find($choice);
        if (!$choiceInstance) throw $this->createNotFoundException();

        //$choiceInstance->setChoNumVotes($choiceInstance->getChoNumVotes() + 1);
        //$this->getDoctrine()->getManager()->persist();
        //$this->getDoctrine()->getManager()->flush();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        //$dql = "UPDATE App:Choice c SET c.cho_num_votes = c.cho_num_votes + 1 WHERE c.cho_id = :choiceId";
        //$query = $em->createQuery($dql)->setParameter("choiceId", $choice);


        $query = $em->getRepository(Choice::class)->createQueryBuilder("c")
            ->update()
            ->set("c.cho_numvotes", "c.cho_numvotes + 1")
            ->where("c.cho_id = :choId")
            ->setParameter("choId", $choice)
            ->getQuery();
        $rows = $query->execute();
        $this->addFlash("notice", "VOTED FOR '{$choiceInstance}', AFFECTED {$rows}");

        return $this->redirectToRoute("votes_listc", ["question" => $choiceInstance->getChoQuestion()->getQuId()]);
    }
}