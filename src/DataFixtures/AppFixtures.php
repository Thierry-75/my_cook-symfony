<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Mark;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Contact;
use App\Entity\Recette;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //user
        $admin = new User();
        $admin->setFullName('administrateur du site')
            ->setPseudo('admin')
            ->setEmail('admin@cook.fr')
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPlainPassword('password');
        $users[] = $admin;
        $manager->persist($admin);
        for ($i = 0; $i < 4; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
            $users[] = $user;
            $manager->persist($user);
        }
        // ingredient
        $ingredients = [];
        for ($i = 0; $i <= 100; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->sentence(2))
                ->setPrice($this->faker->randomFloat(2, 10, 200))
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }
        // recette
        $recettes = [];
        for ($j = 0; $j < 5; $j++) {
            $recette = new Recette();
            $recette->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 240) : 30)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : 2)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : 2)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 100) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recette->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recettes[] = $recette;
            $manager->persist($recette);
        }

        // marks
        foreach ($recettes as $recette) {
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecette($recette);
                $manager->persist($mark);
            }
        }

        // contacts
        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande :°' . $i + 1)
                ->setMessage($this->faker->text());
            $manager->persist($contact);
        }


        $manager->flush();
    }
}
