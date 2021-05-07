<?php


namespace App\Controller;


use App\DTO\DtoBase;
use App\DTO\LoginDto;
use App\DTO\RegisterDto;
use App\DTO\TextDto;
use App\DTO\ChangePasswordDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController
{
    private $passFile = "../templates/editor/users.txt";
    private $dataFile = "../templates/editor/data.txt";
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
     * @Route(name="editor_create", path="/editor/create")
     * @param Request $request
     * @return Response
     */
    public function createPasswordFileAction(Request $request) : Response
    {
        $str = "";
        $str .= "bill\t".password_hash("billpass", PASSWORD_DEFAULT)."\n";
        $str .= "joe\t".password_hash("joepass", PASSWORD_DEFAULT)."\n";
        $str .= "admin\t".password_hash("adminpass", PASSWORD_DEFAULT)."\n";
        file_put_contents($this->passFile, $str);
        return new Response(nl2br($str));
    }

    /**
     * @Route(name="editor_logout", path="/editor/logout")
     * @param Request $request
     * @return Response
     */
    public function logoutAction(Request $request) : Response
    {
        $this->get('session')->clear();
        $this->addFlash("notice", "LOGGED OUT");
        return $this->redirectToRoute("editor");
    }

    /**
     * @Route(name="editor", path="/editor")
     * @param Request $request
     * @return Response
     */
    public function editorAction(Request $request) : Response
    {
        $twig_params = ["filetext" => "", "sessiontext" => "", "form" => null];
        //return $this->render("editor/editor.html.twig", $twig_params);
        $twig_params["sessiontext"] = $this->get("session")->get("customText");
        if (file_exists($this->dataFile)){
            $twig_params["filetext"] = file_get_contents($this->dataFile);
        }
        $sessionUser = $this->get("session")->get("userName");
        if ($sessionUser){
            $dto = new TextDto($this->formFactory, $request);
        }
        else{
            $dto = new LoginDto($this->formFactory, $request);
        }
        /** @var DtoBase $dto */
        $form = $dto->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if($sessionUser){
                $this->processTextInput($dto, $form);
            }
            else{
                $this->processLoginInput($dto);
            }
            return $this->redirectToRoute("editor");
        }
        $twig_params["form"] = $form->createView();
        return $this->render("editor/editor.html.twig", $twig_params);
    }

    /**
     * @Route(path="/editor/register", name="editor_register")
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request): Response{
        $dto = new RegisterDto($this->formFactory, $request);
        $form = $dto->getForm();
        $twig_params["form"] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $str = $dto->getUserName()."\t".password_hash($dto->getUserPass(), PASSWORD_DEFAULT);
            file_put_contents($this->passFile, $str."\n", FILE_APPEND);
            $this->addFlash("notice", "REGISTRATION SUCCESSFUL");
            return $this->redirectToRoute("editor");
        }
        return $this->render("editor/register.html.twig", $twig_params);
    }

    /**
     * @Route (name="editor_profile", path="/profile")
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(Request $request): Response{
        $this->checkLogin();
        $dto = new ChangePasswordDto($this->formFactory, $request);
        $form = $dto->getForm();

        $twig_params["form"] = $form->createView();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentGivenPw = $dto->getCurrentPassword();
            $pwfile = file($this->passFile, FILE_IGNORE_NEW_LINES);
            $str = "";
            foreach ($pwfile as $line){
                $arr = explode("\t", $line);
                if ($arr[0] == $this->get("session")->get("userName") && password_verify($currentGivenPw, $arr[1])){
                    $arr[1] = password_hash($dto->getNewPassword(), PASSWORD_DEFAULT);
                    $this->addFlash("notice", "PASSWORD SUCCESFULLY CHANGED");
                }
                else if ($arr[0] == $this->get("session")->get("userName")){
                    $this->addFlash("notice", "PASSWORD CHANGE FAILED");
                    return $this->render("editor/profile.html.twig", $twig_params);
                }
                $str .= $arr[0]."\t".$arr[1]."\n";
            }
            file_put_contents($this->passFile, $str);
            return $this->redirectToRoute("editor");
        }

        return $this->render("editor/profile.html.twig", $twig_params);
    }

    private function checkLogin(){
        if (!$this->get('session')->has('userName')){
            throw $this->createAccessDeniedException();
        }
    }

    private function processTextInput(TextDto $dto, FormInterface $form)
    {
        $text = $dto->getTextContent();
        if ($form->get("saveToSession")->isClicked()){
            $this->get("session")->set("customText", $text);
            $this->addFlash("notice", "SAVED TO SESSION");
        }
        else{
            file_put_contents($this->dataFile, $text);
            $this->addFlash("notice", "SAVED TO FILE");
        }
    }

    private function processLoginInput(LoginDto $dto)
    {
        $uname = $dto->getUsername();
        $upass = $dto->getUserPass();

        $pwfile = file($this->passFile, FILE_IGNORE_NEW_LINES);
        foreach ($pwfile as $line){
            $arr = explode("\t", $line);
            if($uname == $arr[0] && password_verify($upass, $arr[1])){
                $this->get("session")->set("userName", $arr[0]);
                $this->addFlash("notice", "LOGIN OK");
                return;
            }
        }
        $this->addFlash("notice", "LOGIN FAILED");
    }
}