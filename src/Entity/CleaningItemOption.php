<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use App\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CleaningItemOptionRepository")
 */
class CleaningItemOption
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItem", inversedBy="cleaningItemOptions")
     */
    private $cleaningItem;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return CleaningItemOption
     */
    public function setTitle(string $title): CleaningItemOption
    {
        $this->title = $title;
        return $this;
    }

    public function getCleaningItem(): ?CleaningItem
    {
        return $this->cleaningItem;
    }

    public function setCleaningItem(?CleaningItem $cleaningItem): self
    {
        $this->cleaningItem = $cleaningItem;

        return $this;
    }


}
