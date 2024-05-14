<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Livres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
       for ($j=1;$j<=3;$j++){
        $cat=new Categories();
        $libelle=$faker->name;
          $cat->setLibelle($libelle)
          ->setSlug(strtolower(preg_replace('/[^a-zA-Z0-9-]/','-',$libelle)))
          ->setDescription($faker->sentence);
          $manager->persist($cat);
        for ($i=1; $i <random_int(10,15) ; $i++) { 
            $livre = new Livres();
            $titre=$faker->name();
            $datetime=$faker->dateTime();
            $datetimeimmutable=\DateTimeImmutable::createFromMutable($datetime);
            $livre->setImage($faker->imageUrl())
            ->setTitre($titre)
            ->setEditeur($faker->company())
            ->setISBN($faker->isbn13())
            ->setPrix($faker->numberBetween(10,300))
            ->setEditedAt($datetimeimmutable)
            ->setSlug(strtolower(preg_replace('/[^a-zA-Z0-9-]/','-',$titre)))
            ->setResumer($faker->sentence(20))
            ->setAutheur($faker->userName())
            ->setQte($faker->numberBetween(0,300))
            ->setCategorie($cat);
            $manager->persist($livre);
        }
       }
        $manager->flush();
        
    }

}
