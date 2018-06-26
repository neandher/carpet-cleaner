<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $state;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $itemsTotal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="schedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ScheduleItems", mappedBy="schedule", cascade={"persist"})
     */
    private $scheduleItems;

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
}
