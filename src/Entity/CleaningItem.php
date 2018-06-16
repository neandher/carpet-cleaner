<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use App\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CleaningItemRepository")
 */
class CleaningItem
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    private $displayOrder;

    /**
     * @ORM\Column(type="smallint", length=255)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    private $maxQuantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItemCategory")
     * @Assert\NotNull()
     */
    private $cleaningItemCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CleaningItemOption", mappedBy="cleaningItem", cascade={"persist"})
     */
    private $cleaningItemOptions;

    public function __construct()
    {
        $this->cleaningItemOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCleaningItemCategory(): ?CleaningItemCategory
    {
        return $this->cleaningItemCategory;
    }

    public function setCleaningItemCategory(?CleaningItemCategory $cleaningItemCategory): self
    {
        $this->cleaningItemCategory = $cleaningItemCategory;

        return $this;
    }

    /**
     * @return Collection|CleaningItemOption[]
     */
    public function getCleaningItemOptions(): Collection
    {
        return $this->cleaningItemOptions;
    }

    public function addCleaningItemOption(CleaningItemOption $cleaningItemOption): self
    {
        if (!$this->cleaningItemOptions->contains($cleaningItemOption)) {
            $this->cleaningItemOptions[] = $cleaningItemOption;
            $cleaningItemOption->setCleaningItem($this);
        }

        return $this;
    }

    public function removeCleaningItemOption(CleaningItemOption $cleaningItemOption): self
    {
        if ($this->cleaningItemOptions->contains($cleaningItemOption)) {
            $this->cleaningItemOptions->removeElement($cleaningItemOption);
            // set the owning side to null (unless already changed)
            if ($cleaningItemOption->getCleaningItem() === $this) {
                $cleaningItemOption->setCleaningItem(null);
            }
        }

        return $this;
    }

    public function getMaxQuantity(): ?int
    {
        return $this->maxQuantity;
    }

    public function setMaxQuantity(int $maxQuantity): self
    {
        $this->maxQuantity = $maxQuantity;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
