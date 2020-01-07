<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $products = [];
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName(sprintf('Product %04d', $i));
            $manager->persist($product);
            $products[] = $product;
        }
        $manager->flush();
        
        for ($i = 0; $i < 10; $i++) {
            $this->addReference(self::buildReferenceName($i), $products[$i]);
        }
    }
    
    public static function buildReferenceName($code)
    {
        return sprintf('product-%04d', $code);
    }
}
