<?php

namespace App\DTO;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChangePasswordDto extends DtoBase
{
    /** @var string */
    private $currentPassword = "";
    /** @var string */
    private $newPassword = "";

    /**
     * @return string
     */
    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     * @return ChangePasswordDto
     */
    public function setCurrentPassword(string $currentPassword): ChangePasswordDto
    {
        $this->currentPassword = $currentPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     * @return ChangePasswordDto
     */
    public function setNewPassword(string $newPassword): ChangePasswordDto
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function __construct(FormFactoryInterface $formFactory, $request)
    {
        parent::__construct($formFactory, $request);
    }


    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add("currentPassword", TextType::class);
        $builder->add("newPassword", TextType::class);
        $builder->add("CHANGE PASSWORD", SubmitType::class);
        return $builder->getForm();
    }
}