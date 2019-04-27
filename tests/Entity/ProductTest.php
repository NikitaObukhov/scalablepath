<?php

namespace App\Tests\Repository;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;


    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testProductSoftDelete()
    {
        /**
         * Test repository agains existing database
         *
         */
        $product = $this->createNewProduct();
        $repository = $this->entityManager
            ->getRepository(Product::class);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $product->setIsDeleted(true);
        $this->entityManager->merge($product);
        $this->entityManager->flush();
        $notDeleted = $repository->findNotDeleted();
        $found = false;
        foreach($notDeleted as $existingProduct) {
            if ($existingProduct->getId() == $product->getId()) {
                $found = true;
            }

        }
        $this->assertEquals($found, false);
        // clearup
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    protected function createNewProduct()
    {
        $product = new Product();
        $product->setPrice(100);
        $product->setName('test');
        $product->setDescription('test');
        return $product;
    }
}