<?php
namespace App\DTO;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class DtoBase
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var Request  */
    protected $request;

    public function __construct($formFactory, $request)
    {
        $this->formFactory = $formFactory;
        $this->request = $request;
    }

    public abstract function getForm() : FormInterface;
}