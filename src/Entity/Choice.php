<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Choice
 * @package App\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="choices")
 * @ORM\HasLifecycleCallbacks
 */
class Choice
{
    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $cho_id;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cho_inserted;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cho_modified;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cho_visible;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    private $cho_text;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cho_numvotes;

    /**
     * @var Question|null
     * @ORM\JoinColumn(name="cho_question", referencedColumnName="qu_id")
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="qu_choices")
     */
    private $cho_question;

    public function __toString()
    {
        $question = $this->cho_question ? $this->cho_question->getQuText() : "N/A";
        return "{$question} / {$this->cho_text} / {$this->cho_numvotes}";
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(){
        $this->cho_modified = new DateTime();
        if ($this->cho_inserted == null){
            $this->cho_inserted = new DateTime();
        }
    }

    //AUTOGENERATE GETTERS AND SETTERS, REMOVE SETTER OF ID

    /**
     * @return int
     */
    public function getChoId(): int
    {
        return $this->cho_id;
    }

    /**
     * @return DateTime|null
     */
    public function getChoInserted(): ?DateTime
    {
        return $this->cho_inserted;
    }

    /**
     * @param DateTime|null $cho_inserted
     */
    public function setChoInserted(?DateTime $cho_inserted): void
    {
        $this->cho_inserted = $cho_inserted;
    }

    /**
     * @return DateTime|null
     */
    public function getChoModified(): ?DateTime
    {
        return $this->cho_modified;
    }

    /**
     * @param DateTime|null $cho_modified
     */
    public function setChoModified(?DateTime $cho_modified): void
    {
        $this->cho_modified = $cho_modified;
    }

    /**
     * @return bool|null
     */
    public function getChoVisible(): ?bool
    {
        return $this->cho_visible;
    }

    /**
     * @param bool|null $cho_visible
     */
    public function setChoVisible(?bool $cho_visible): void
    {
        $this->cho_visible = $cho_visible;
    }

    /**
     * @return string|null
     */
    public function getChoText(): ?string
    {
        return $this->cho_text;
    }

    /**
     * @param string|null $cho_text
     */
    public function setChoText(?string $cho_text): void
    {
        $this->cho_text = $cho_text;
    }

    /**
     * @return int|null
     */
    public function getChoNumvotes(): ?int
    {
        return $this->cho_numvotes;
    }

    /**
     * @param int|null $cho_numvotes
     */
    public function setChoNumvotes(?int $cho_numvotes): void
    {
        $this->cho_numvotes = $cho_numvotes;
    }

    /**
     * @return Question|null
     */
    public function getChoQuestion(): ?Question
    {
        return $this->cho_question;
    }

    /**
     * @param Question|null $cho_question
     */
    public function setChoQuestion(?Question $cho_question): void
    {
        $this->cho_question = $cho_question;
    }

}