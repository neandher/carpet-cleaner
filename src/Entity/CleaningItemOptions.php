<?php

namespace App\Entity;

use App\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $cleaningItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItemOption")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cleaningItemOption;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $amount;

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

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
