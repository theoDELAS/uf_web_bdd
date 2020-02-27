<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Création des différentes catégories
        $categories = array(
            'Action',
            'Guerre',
            'Aventure',
            'Stratégie',
            'Enigmes'
        );

        $categoriesTab = [];

        for ($i = 0; $i < 5; $i++)
        {
            $category = new Category();
            $category->setName($categories[$i]);

            $categoriesTab[] = $category;

            $manager->persist($category);
        }

        // Les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++)
        {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

         // Génere des produits fake
        for ($i = 0; $i < 10; $i++) {
            $coverImage = "https://picsum.photos/id/".mt_rand(1, 500)."/750/350";

            $user = $users[mt_rand(0, count($users) - 1)];

            $product = new Product();
            $product->setTitle($faker->word);
            $product->setDescription($faker->text);
            $product->setPrice($faker->numberBetween($min = 0, $max = 50));
            $product->setCoverImage($coverImage);
            $product->setCategory($categoriesTab[mt_rand(0, 4)]);
            $product->setAuthor($user);


            // générer des images randoms pour chaque produits
            for ($j = 1; $j <= mt_rand(2, 5); $j++)
            {
                $image = new Image();

                $image->setUrl("https://picsum.photos/id/".mt_rand(1, 500)."/640/480")
                    ->setCaption($faker->sentence())
                    ->setProduct($product);

                $manager->persist($image);
            }
            $manager->persist($product);
        }
        $manager->flush();
    }
}
