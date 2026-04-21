<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    // Test de la méthode computeTVA pour un produit alimentaire
    public function testcomputeTVAFoodProduct()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, 20);
        $this->assertSame(1.1, $product->computeTVA());
    }

    // Test de la méthode computeTVA pour un autre type de produit
    public function testComputeTVAOtherProduct()
    {
        $product = new Product('Un autre produit', 'Un autre type de produit', 20);
        $this->assertSame(3.92, $product->computeTVA());
    }

    // Test de la méthode computeTVA pour un prix négatif
    public function testNegativePriceComputeTVA()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, -20);
        $this->expectException('Exception');
        $product->computeNegativeTVA();
    }

    /**
     * @dataProvider pricesForFoodProduct
     * Test de la méthode computeTVA pour différents prix de produits alimentaires
     */
    public function testcomputeTVAFoodProductProvider($price, $expectedTva)
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, $price);
        $this->assertSame($expectedTva, $product->computeTVA());
    }

    // Data provider pour les tests de computeTVA pour les produits alimentaires
    public function pricesForFoodProduct()
    {
        return [
            [0, 0.0],
            [20, 1.1],
            [100, 5.5]
        ];
    }
}
