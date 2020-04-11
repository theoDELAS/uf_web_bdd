<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

class Search {
    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private $search;

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return Search
     */
    public function setSearch(?string $search): Search
    {
        $this->search = $search;
        return $this;
    }
}