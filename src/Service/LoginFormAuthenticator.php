<?php

namespace App\Service;


use App\DTO\LoginDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait; //For redirect...
    /** @var EntityManagerInterface  */
    private $em;
    /** @var RouterInterface  */
    private $router;
    /** @var UserPasswordEncoderInterface  */
    private $encoder;
    /** @var FormFactoryInterface  */
    private $formFactory;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $encoder,
        FormFactoryInterface $ff){
        $this->em = $em;
        $this->router = $router;
        $this->encoder = $encoder;
        $this->formFactory = $ff;
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl()
    {
        return $this->router->generate("app_login");
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->attributes->get("_route") === "app_login" && $request->isMethod("POST");
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        $dto = new LoginDto($this->formFactory, $request);
        $form = $dto->getForm();
        $form->handleRequest($request);
        if(!$form->isValid() || !$form->isSubmitted()){
            throw new InvalidCsrfTokenException("INVALID FORM");
        }
        $request->getSession()->set(Security::LAST_USERNAME, $dto->getUserName());
        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var LoginDto $credentials */
        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $credentials->getUserName()]);
        if(!$user){
            throw new CustomUserMessageAuthenticationException("BAD EMAIL");
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var LoginDto $credentials */
        return $this->encoder->isPasswordValid($user, $credentials->getUserPass());
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if($targetPath){
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate("app_login"));
    }


}