<?php

namespace App\Data;

use App\Entity\category;

class SearchProduct
{
    /**@var string */
    public $string ='';

    /**@var Category[] */
    public $categories = [];

    /**@var int|null */
    private $maxPrice;
    
    /**@var int|null */
    private $minPrice;

    /**
     * Get the value of maxPrice
     */ 
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * Set the value of maxPrice
     *
     * @return  self
     */ 
    public function setMaxPrice(int $maxPrice)
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * Get the value of minPrice
     */ 
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * Set the value of minPrice
     *
     * @return  self
     */ 
    public function setMinPrice(int $minPrice)
    {
        $this->minPrice = $minPrice;

        return $this;
    }
}