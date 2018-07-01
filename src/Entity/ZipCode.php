<?php

namespace App\Entity;

use App\Resource\Model\TimestampableTrait;
use App\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ZipCodeRepository")
 * @UniqueEntity(fields={"description"}, message="This zip code is already in use.")
 */
class ZipCode
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\LessThan(value="99999999.99")
     */
    private $percentage;

    public function getId()
    {
        return $this->id;
    }

    public function getDescription(): ?int
    {
        return $this->description;
    }

    public function setDescription(int $description): self
    {
        $this->description = $description;

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
