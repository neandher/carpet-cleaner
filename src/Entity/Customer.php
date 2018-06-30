<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use App\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @UniqueEntity(fields={"phoneNumber"}, message="This phone number is already in use.")
 */
class Customer
{
    use ToggleableTrait, TimestampableTrait;

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
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomerAddresses", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     * @Assert\Count(min="1")
     */
    private $customerAddresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="customer")
     */
    private $schedules;

    public function __construct()
    {
        $this->customerAddresses = new ArrayCollection();
        $this->schedules = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection|CustomerAddresses[]
     */
    public function getCustomerAddresses(): Collection
    {
        return $this->customerAddresses;
    }

    public function addCustomerAddress(CustomerAddresses $customerAddress): self
    {
        if (!$this->customerAddresses->contains($customerAddress)) {
            $this->customerAddresses[] = $customerAddress;
            $customerAddress->setCustomer($this);
        }

        return $this;
    }

    public function removeCustomerAddress(CustomerAddresses $customerAddress): self
    {
        if ($this->customerAddresses->contains($customerAddress)) {
            $this->customerAddresses->removeElement($customerAddress);
            // set the owning side to null (unless already changed)
            if ($customerAddress->getCustomer() === $this) {
                $customerAddress->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setCustomer($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getCustomer() === $this) {
                $schedule->setCustomer(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
