<?php

namespace App\Controller;

use App\DTO\ChoiceDto;
use App\DTO\QuestionDto;
use App\Entity\Choice;
use App\Entity\Question;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VotesController extends AbstractController
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * EditorController constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route(path="/votes", name="votes_listq")
     */
    public function listqAction(Request $request): Response{
        //SHOULD NOT USE DOCTRINE IN CONTROLLER! USE IT IN SERVICE!
        //SHOULD USE A VIEWMODEL CLASS
        //SHOULD NOT SEND ENTITY TO VIEW/FORM
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();

        $dto = new QuestionDto($this->formFactory, $request);
        $form = $dto->getForm();
        $form->handleRequest($request);
        $twig_params = ["questions" => $questions, "form" => $form];
        if ($form->isSubmitted() && $form->isValid()){
            $this->addQuestion($dto);
            $this->addFlash("notice", "QUESTION ADDED");
            return $this->redirectToRoute("votes_listq");
        }

        $twig_params["form"] = $form->createView();
        return $this->render("votes/questions.html.twig", $twig_params);
    }

    /**
     * @param Request $request
     * @param int $question
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route(path="/votes/question/{question}", name="votes_listc", requirements={"question": "\d+"})
     */
    public function listcAction(Request $request, int $question): Response{
        /** @var Question $questionInstance */
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->find($question);
        if (!$questionInstance) throw $this->createNotFoundException();

        $dto = new ChoiceDto($this->formFactory, $request);
        $form = $dto->getForm();
        $form->handleRequest($request);
        $twig_params = ["choices" => $questionInstance->getQuChoices(), "form" => $form];
        if ($form->isSubmitted() && $form->isValid()){
            $this->addChoice($dto, $questionInstance);
            $this->addFlash("notice", "CHOICE ADDED");
            return $this->redirectToRoute("votes_listc", ["question" => $question]);
        }

        $twig_params["form"] = $form->createView();
        return $this->render("votes/choices.html.twig", $twig_params);
    }

    /**
     * @param Request $request
     * @param int $question
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route(path="/votes/question/delete/{question}", name="votes_del_question", requirements={"question": "\d+"})
     */
    public function deleteqAction(Request $request, int $question): Response{
        /** @var Choice $choiceInstance */
        $questionInstance = $this->getDoctrine()->getRepository(Question::class)->find($question);
        if (!$questionInstance) throw $this->createNotFoundException();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Choice[] $choices_to_delete */
        $choices_to_delete = $this->getDoctrine()->getRepository(Choice::class)->findBy(array('cho_question' => $questionInstance));
        foreach ($choices_to_delete as $key => $value){
            $em->remove($value);
            $em->flush();
        }

        $em->remove($questionInstance);
        $em->flush();
        $this->addFlash("notice", "QUESTION REMOVED");

        return $this->redirectToRoute("votes_listq");
    }

    /**
     * @param Request $request
     * @param int $choice
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route(path="/votes/choice/delete/{choice}", name="votes_del_choice", requirements={"choice": "\d+"})
     */
    public function deletecAction(Request $request, int $choice): Response{
        /** @var Choice $choiceInstance */
        $choiceInstance = $this->getDoctrine()->getRepository(Choice::class)->find($choice);
        if (!$choiceInstance) throw $this->createNotFoundException();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->remove($choiceInstance);
        $em->flush();
        $this->addFlash("notice", "CHOICE REMOVED");

        return $this->redirectToRoute("votes_listc", ["question" => $choiceInstance->getChoQuestion()->getQuId()]);
    }

    /**
     * @param ChoiceDto $dto
     * @param Question $question
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function addChoice(ChoiceDto $dto, Question $question){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $text = $dto->getTextContent();
        $ans = new Choice();
        $ans->setChoVisible(true);
        $ans->setChoNumvotes(0);
        $ans->setChoText($text);
        $ans->setChoQuestion($question);
        $em->persist($ans);
        $em->flush();
    }

    /**
     * @param QuestionDto $dto
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function addQuestion(QuestionDto $dto){

        $text = $dto->getTextContent();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $qu = new Question();
        $qu->setQuText($text);
        $em->persist($qu);
        $em->flush();
    }

    /**
     * @param Request $request
     * @param int $choice
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