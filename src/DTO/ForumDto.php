<?php

namespace App\DTO;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ForumDto extends DtoBase
{
    /** @var string */
    private $textContent = "";
    /** @var string */
    private $category;

    /**
     * @return string
     */
    public function getTextContent(): string
    {
        return $this->textContent;
    }

    /**
     * @param string $textContent
     */
    public function setTextContent(string $textContent): void
    {
        $this->textContent = $textContent;
    }

    public function __construct(FormFactoryInterface $formFactory,Request $request, string $category)
    {
        parent::__construct( $formFactory, $request);
        $this->category = $category;
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add("textContent", TextareaType::class, ["required"=>true, "label"=>"Add {$this->category}"]);
        $builder->add("Save", SubmitType::class);
        return $builder->getForm();
    }


}