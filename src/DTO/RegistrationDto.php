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
    private $firstName = "";

    /** @var string */
    private $lastName = "";

    /** @var string */
    private $email = "";

    /** @var string */
    private $clearPassword = "";

    /** @var bool */
    private $gdprAgreed = false;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
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
    public function getClearPassword(): string
    {
        return $this->clearPassword;
    }

    /**
     * @param string $clearPassword
     */
    public function setClearPassword(string $clearPassword): void
    {
        $this->clearPassword = $clearPassword;
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
        $builder->add('firstName', TextType::class, ["required" => true]);
        $builder->add('lastName', TextType::class, ["required" => true]);
        $builder->add('email', EmailType::class, ["required" => true]);
        $builder->add('clearPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The passwords must match!',
            'required' => true,
            'first_options' => ["label" => "Password"],
            'second_options' => ["label" => "Confirm password"],
            'constraints' => [
                new NotBlank(['message' => 'Password cannot be empty']),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password must be at least {{ limit }} characters long.',
                    'max' => 4096
                ])
            ]
        ]);
        $builder->add('gdprAgreed', CheckboxType::class, ["constraints" => [
            new IsTrue(["message" => "You must agree to the GDPR rules!"])
        ]]);
        $builder->add('Register user', SubmitType::class);
        return $builder->getForm();
    }
}