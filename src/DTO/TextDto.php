<?php


namespace App\DTO;


use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;

class TextDto extends DtoBase
{
    /** @var string */
    private $textContent = "";

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

    public function __construct($formFactory, $request)
    {
        parent::__construct($formFactory, $request);
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add("textContent", TextareaType::class);
        $builder->add("saveToSession", SubmitType::class);
        $builder->add("saveToFile", SubmitType::class);
        return $builder->getForm();
    }


}