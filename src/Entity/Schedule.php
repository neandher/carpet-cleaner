<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use App\Validator\ScheduleAvailable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    use TimestampableTrait;

    const STATE_NEW = 'new';
    const STATE_CONFIRMED = 'confirmed';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Groups({"schedule"})
     */
    private $state = self::STATE_NEW;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Groups({"schedule", "scheduleItems"})
     */
    private $itemsDiscount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"schedule", "scheduleItems"})
     */
    private $itemsSubTotal;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"schedule", "scheduleItems"})
     */
    private $itemsTotal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="schedules", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"schedule"})
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @ScheduleAvailable()
     * @Groups({"schedule"})
     */
    private $startDateAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Groups({"schedule"})
     */
    private $endDateAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"schedule"})
     */
    private $instructions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ScheduleItems", mappedBy="schedule", cascade={"persist"})
     * @Groups({"schedule","scheduleItems"})
     */
    private $scheduleItems;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PromotionCoupon")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"schedule","scheduleItems"})
     */
    private $promotionCoupon;

    public function __construct()
    {
        $this->scheduleItems = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    public function setItemsTotal($itemsTotal): self
    {
        $this->itemsTotal = $itemsTotal;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|ScheduleItems[]
     */
    public function getScheduleItems(): Collection
    {
        return $this->scheduleItems;
    }

    public function addScheduleItem(ScheduleItems $scheduleItem): self
    {
        if (!$this->scheduleItems->contains($scheduleItem)) {
            $this->scheduleItems[] = $scheduleItem;
            $scheduleItem->setSchedule($this);
        }

        return $this;
    }

    public function removeScheduleItem(ScheduleItems $scheduleItem): self
    {
        if ($this->scheduleItems->contains($scheduleItem)) {
            $this->scheduleItems->removeElement($scheduleItem);
            // set the owning side to null (unless already changed)
            if ($scheduleItem->getSchedule() === $this) {
                $scheduleItem->setSchedule(null);
            }
        }

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): self
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function getStartDateAt(): ?\DateTimeInterface
    {
        return $this->startDateAt;
    }

    public function setStartDateAt(?\DateTimeInterface $startDateAt): self
    {
        $this->startDateAt = $startDateAt;

        return $this;
    }

    public function getEndDateAt(): ?\DateTimeInterface
    {
        return $this->endDateAt;
    }

    public function setEndDateAt(?\DateTimeInterface $endDateAt): self
    {
        $this->endDateAt = $endDateAt;

        return $this;
    }

    public function getPromotionCoupon(): ?PromotionCoupon
    {
        return $this->promotionCoupon;
    }

    public function setPromotionCoupon(?PromotionCoupon $promotionCoupon): self
    {
        $this->promotionCoupon = $promotionCoupon;

        return $this;
    }

    public function getItemsDiscount()
    {
        return $this->itemsDiscount;
    }

    public function setItemsDiscount($itemsDiscount): self
    {
        $this->itemsDiscount = $itemsDiscount;

        return $this;
    }

    public function getItemsSubTotal()
    {
        return $this->itemsSubTotal;
    }

    public function setItemsSubTotal($itemsSubTotal): self
    {
        $this->itemsSubTotal = $itemsSubTotal;

        return $this;
    }
}
