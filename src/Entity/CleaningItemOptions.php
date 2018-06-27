<?php

namespace App\Entity;

use App\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CleaningItemOptionsRepository")
 */
class CleaningItemOptions
{
    use ToggleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItem", inversedBy="cleaningItemOptions")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     */
    private $cleaningItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItemOption")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $cleaningItemOption;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\LessThan(value="99999999.99")
     */
    private $percentage;

    public function getId()
    {
        return $this->id;
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

    public function getCleaningItemOption(): ?CleaningItemOption
    {
        return $this->cleaningItemOption;
    }

    public function setCleaningItemOption(?CleaningItemOption $cleaningItemOption): self
    {
        $this->cleaningItemOption = $cleaningItemOption;

        return $this;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

    public function setPercentage($percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }
}
