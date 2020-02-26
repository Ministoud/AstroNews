<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\User;
use App\Entity\Section;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::Create('fr_FR');
        $users = array();
        $articles = array();
        $sections = array();

        

        for ($i=0; $i < 10; $i++) { 
            $sections[] = $section = (new Section())
                ->setSecName($faker->word());

            $manager->persist($section);
        }

        for ($i=0; $i < 10; $i++) { 
            $articles[] = $article = (new Article())
                ->setArtName($faker->words(6, true))
                ->setArtContent($faker->paragraphs(5, true))
                ->setArtCreationDate($faker->dateTime())
                ->setArtImage("cat.jpeg")
                ->addArtSection($faker->randomElement($sections));

            $manager->persist($article);
        }

        for ($i=0; $i < 5; $i++) { 
            $users[] = $user = (new User())
                ->setUseFirstName($faker->firstName())
                ->setUseLastName($faker->lastName())
                ->setUseEmail($faker->email())
                ->setUsePassword($faker->password())
                ->addUseFollowedSection($faker->randomElement($sections));

            $users[] = $user;
            $manager->persist($user);
        }

        for ($i=0; $i < count($articles); $i++) { 
            if ($i < count($users))
            {
                $articles[$i]->setArtAuthor($users[$i]);
            }
            else
            {
                $articles[$i]->setArtAuthor($faker->randomElement($users));
            }
        }

        $manager->flush();
    }
}
