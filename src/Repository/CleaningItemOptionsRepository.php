<?php

namespace App\Repository;

use App\Entity\CleaningItemOptions;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CleaningItemOptionsRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CleaningItemOptions::class);
    }

}
