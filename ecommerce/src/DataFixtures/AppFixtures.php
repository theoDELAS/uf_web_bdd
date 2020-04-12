<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Favorite;
use App\Entity\Panier;
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

        $favorite = new Favorite();
        $manager->persist($favorite);

        $adminUser = new User();
        $panier = new Panier();
        $panier->setAmount(0);
        $manager->persist($panier);
        $adminUser->setFirstName('Théo')
            ->setLastName('Delas')
            ->setEmail('theo.delas@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://randomuser.me/portraits/men/9.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole)
            ->setBalance(mt_rand(0, 1000))
            ->setPanier($panier)
            ->setBirthday('19/03/1997')
            ->setFavorite($favorite);

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
            'Switch'
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

            $panier =  new Panier();
            $panier->setAmount(0);
            $manager->persist($panier);

            $favorite = new Favorite();
            $manager->persist($favorite);

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $birthday = $faker->date('d-m-Y');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture)
                ->setBalance(mt_rand(0, 1000))
                ->setPanier($panier)
                ->setBirthday($birthday)
                ->setFavorite($favorite);


            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();
    }
}
