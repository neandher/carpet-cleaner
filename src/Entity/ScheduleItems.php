<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleItemsRepository")
 */
class ScheduleItems
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"scheduleItems"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"scheduleItems"})
     */
    private $unitPrice;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"scheduleItems"})
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItem")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"scheduleItems"})
     */
    private $cleaningItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CleaningItemOption")
     * @Groups({"scheduleItems"})
     */
    private $cleaningItemOption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Schedule", inversedBy="scheduleItems")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $schedule;

    public function getId()
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setUnitPrice($unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total): self
    {
        $this->total = $total;

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

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

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
}
