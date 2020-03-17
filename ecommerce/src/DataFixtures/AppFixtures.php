<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Platform;
use App\Entity\Product;
use App\Entity\Role;
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

        // Role admin
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Théo')
            ->setLastName('Delas')
            ->setEmail('theo.delas@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://randomuser.me/portraits/men/13.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole)
            ->setBalance(mt_rand(0, 1000));
        $manager->persist($adminUser);

        // Création des différentes catégories
        $categories = array(
            'Action',
            'Guerre',
            'Aventure',
            'Stratégie',
            'Enigme',
            'Autre'
        );

        $categoriesTab = [];

        for ($i = 0; $i < sizeof($categories); $i++)
        {
            $category = new Category();
            $category->setName($categories[$i]);

            $categoriesTab[] = $category;

            $manager->persist($category);
        }

        // La plateforme
        $platforms = array(
            'PC',
            'XBOX',
            'PS4',
            'Stadia'
        );

        $platformsTab = [];

        for ($i = 0; $i < sizeof($platforms); $i++) {
            $platform = new Platform();
            $platform->setName($platforms[$i]);

            $platformsTab[] = $platform;

            $manager->persist($platform);
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
                ->setPicture($picture)
                ->setBalance(mt_rand(0, 1000));

            $manager->persist($user);
            $users[] = $user;
        }

         // Génere des produits fake
        for ($i = 0; $i < 20; $i++) {
            $coverImage = "https://picsum.photos/id/".mt_rand(1, 500)."/750/350";

            $user = $users[mt_rand(0, count($users) - 1)];

            $product = new Product();
            $product->setTitle($faker->word);
            $product->setDescription($faker->text);
            $product->setPrice($faker->numberBetween($min = 0, $max = 50));
            $product->setCoverImage($coverImage);
            $product->setCategory($categoriesTab[mt_rand(0, sizeof($categories) - 1)]);

            foreach ($platformsTab as $platform) {
                if (mt_rand(0, 5) > 2) {
                    $product->addPlatform($platform);
                }
            }
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

            // Gestion des commentaires
            $buyer = $users[mt_rand(0, count($users) -1)];

            if(mt_rand(0, 1)) {
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1, 5))
                    ->setAuthor($buyer)
                    ->setProduct($product);
                $manager->persist($comment);
            }
            $manager->persist($product);
        }
        $manager->flush();
    }
}
