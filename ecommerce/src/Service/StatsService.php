<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getStats()
    {
        $users = $this->getUsersCount();
        $products = $this->getProductsCount();
        $comments = $this->getCommentsCount();
        $ps4 = $this->getPLaystationCount();
        $xbox = $this->getPLaystationCount();
        $pc = $this->getPLaystationCount();
        $stadia = $this->getPLaystationCount();

        return compact('users', 'products', 'comments', 'ps4', 'xbox', 'pc', 'stadia');
    }

    public function getCount($entity, $alias)
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity'.$entity.' ' .$alias)->getSingleScalarResult();
    }

    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getProductsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Product p')->getSingleScalarResult();
    }

    public function getCommentsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getPLaystationCount()
    {
        return $this->manager->createQuery("SELECT COUNT(p)FROM App\Entity\Product p JOIN p.platforms pl WHERE pl.name = 'PS4'")->getSingleScalarResult();
    }

    public function getXboxCount()
    {
        return $this->manager->createQuery("SELECT COUNT(p)FROM App\Entity\Product p JOIN p.platforms pl WHERE pl.name = 'XBOX'")->getSingleScalarResult();
    }

    public function getPcCount()
    {
        return $this->manager->createQuery("SELECT COUNT(p)FROM App\Entity\Product p JOIN p.platforms pl WHERE pl.name = 'PC'")->getSingleScalarResult();
    }

    public function getStadiaCount()
    {
        return $this->manager->createQuery("SELECT COUNT(p)FROM App\Entity\Product p JOIN p.platforms pl WHERE pl.name = 'Stadia'")->getSingleScalarResult();
    }

    public function getProductsStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, p.title, p.id
             FROM App\Entity\Comment c
             JOIN c.product p
             GROUP BY p
             ORDER BY note ' . $direction
        )
            ->setMaxResults(5)
            ->getResult();
    }
}