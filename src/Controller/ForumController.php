<?php

namespace App\Controller;

use App\DTO\ForumDto;
use App\Model\MessageListModel;
use App\Model\MessageModel;
use App\Model\TopicListModel;
use App\Model\TopicModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * ForumController constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    private function checkLogin(){
        if (!$this->get('session')->has('userName')){
            throw $this->createAccessDeniedException();
        }
    }

    private function dtoToString(ForumDto $dto) : string{
        $uname = $this->get('session')->get('userName');
        $now = date('Y-m-d H:i:s');
        return "{$uname}|{$now}|{$dto->getTextContent()}\n";
    }

    /**
     * @Route(path="/forum/topics", name="forum_topic_list")
     * @param Request $request
     * @return Response
     */
    public function listTopicsAction(Request $request): Response{
        $this->checkLogin();
        $fname = "../templates/forum/topics.txt";
        $dto = new ForumDto($this->formFactory, $request, "topic");
        $form = $dto->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            file_put_contents($fname, $dto->getTextContent()."\n", FILE_APPEND);
            $this->addFlash('notice', 'TOPIC ADDED');
            return $this->redirectToRoute("forum_topic_list");
        }

        $topiclist = array();
        if (file_exists($fname)){
            $lines = file($fname);
            foreach($lines as $key => $line){
                $topic = new TopicModel();
                $topic->setId($key)->setName($line);
                $topiclist[] = $topic;
            }
        }

        $model = new TopicListModel();
        $model->setTopicList($topiclist)->setTopicForm($form->createView());

        return $this->render("forum/topiclist.html.twig", ["model" => $model]);
    }

    /**
     * @Route(path="/forum/messages/{topic}", name="forum_msg_list", requirements={"topic": "\d+" })
     * @param Request $request
     * @param int $topic
     * @return Response
     */
    public function listMessagesAction(Request $request, int $topic): Response{
        $this->checkLogin();
        $fname = "../templates/forum/messages_{$topic}.txt";
        $dto = new ForumDto($this->formFactory, $request, "message");
        $form = $dto->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            file_put_contents($fname, $this->dtoToString($dto), FILE_APPEND);
            $this->addFlash("notice", "MESSAGE ADDED");
            return $this->redirectToRoute("forum_topic_list", ["topic" => $topic]);
        }

        $messages = array();
        if (file_exists($fname)){
            $lines = file($fname);
            foreach ($lines as $line){
                $data = explode("|", $line);
                $msg = new MessageModel();
                $msg->setUserName($data[0])
                    ->setTimestamp($data[1])
                    ->setText($data[2]);
                $messages[] = $msg;

            }
        }

        $model = new MessageListModel();
        $model->setMessageList($messages)->setMessageForm($form->createView());

        return $this->render("forum/messagelist.html.twig", ["model" => $model]);
    }

}