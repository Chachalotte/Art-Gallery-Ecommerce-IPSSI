<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 100 products artistes! Bam!
        for ($i = 0; $i < 100; $i++) {
            $rand = ['F', 'M', 'A'][rand(0,2)];
            $product = new Product();
            $product->setName('product '.$i);
            $product->setPrice(mt_rand(10, 100));
            $product->setImg('');
            $product->setColor('red');
            $product->setDescription('Lorem Ipsum', $i);

            $product->setName('product '.$i);
            $manager->persist($product);
        }
        $manager->flush();

        for ($i = 0; $i < 100; $i++) {
            $rand = ['F', 'M', 'A'][rand(0,2)];
            $artist = new Artist();
            $artist->setName('artist '.$i);
            $artist->setFirstname('ouah' .$i);
            $artist->setGender($rand);
            $artist->setAvatar($rand, ".png");
            $artist->setDescription('Lorem Ipsum', $i);

            $manager->persist($artist);
        }

        $manager->flush();

    }
}

?>
