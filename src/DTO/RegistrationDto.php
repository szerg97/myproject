<?php


namespace App\DTO;

//Should NEVER USE entities for forms / VM
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationDto extends DtoBase
{
    /** @var string */
    private $firstname = "";

    /** @var string */
    private $lastname = "";

    /** @var string */
    private $email = "";

    /** @var string */
    private $clearPass = "";

    /** @var bool */
    private $gdprAgreed = false;

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getClearPass(): string
    {
        return $this->clearPass;
    }

    /**
     * @param string $clearPass
     */
    public function setClearPass(string $clearPass): void
    {
        $this->clearPass = $clearPass;
    }

    /**
     * @return bool
     */
    public function isGdprAgreed(): bool
    {
        return $this->gdprAgreed;
    }

    /**
     * @param bool $gdprAgreed
     */
    public function setGdprAgreed(bool $gdprAgreed): void
    {
        $this->gdprAgreed = $gdprAgreed;
    }

    public function __construct(FormFactoryInterface $formFactory, $request)
    {
        parent::__construct($formFactory, $request);
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add('firstname', TextType::class, ['required' => true]);
        $builder->add('lastname', TextType::class, ['required' => true]);
        $builder->add('email', EmailType::class, ['required' => true]);
        $builder->add('clearPass', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The passwords must match!',
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Confirm password'],
            'constraints' => [
                new NotBlank(['message' => 'Password cannot be empty']),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password must be at least {{ limit }} characters long.',
                    'max' => 4096
                ])
            ]
        ]);
        $builder->add('gdprAgreed', CheckboxType::class, ['constraints' => [
            new IsTrue(['message' => 'You must agree to the GDPR rules!'])
        ]]);
        $builder->add('regsterUser', SubmitType::class);
        return $builder->getForm();
    }
}