<?php

namespace App\Entity;

use Exception;

/**
 * Class Product
 * 
 * Un produit a un nom, un type et un prix. Il peut calculer la TVA à appliquer en fonction de son type.
 * Le taux de TVA pour les produits alimentaires est de 5.5%, tandis que pour les autres produits, il est de 19.6%.
 * 
 */
class Product
{
    const FOOD_PRODUCT = 'food';
    private $name;
    private $type;
    private $price;
    public function __construct($name, $type, $price)
    {
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
    }

    // Méthode pour calculer la TVA à appliquer en fonction du type de produit
    public function computeTVA()
    {
        if (self::FOOD_PRODUCT == $this->type) {
            return $this->price * 0.055;
        }
        return $this->price * 0.196;
    }

    // Méthode pour vérifier que le prix n'est pas négatif avant de calculer la TVA
    public function computeNegativeTVA()
    {
        if ($this->price < 0) {
            throw new Exception('The TVA cannot be negative.');
        }
    }
}
