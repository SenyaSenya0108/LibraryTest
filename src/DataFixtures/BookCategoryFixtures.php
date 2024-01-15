<?php

namespace App\DataFixtures;

use App\Entity\BookCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookCategoryFixtures extends Fixture
{
    public const NEW = 'NEW';

    public function load(ObjectManager $manager): void
    {
        $bookCategory = new BookCategory();
        $bookCategory->setName(self::NEW);

        $manager->persist($bookCategory);
        $manager->flush();
    }
}
