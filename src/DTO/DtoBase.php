<?php
namespace App\Dto;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class DtoBase
{
    /** @var FormFactory */
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