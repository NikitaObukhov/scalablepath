<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="product", methods={"GET"})
     */
    public function index()
    {
        $repository = $this->get('doctrine')
            ->getRepository(Product::class);
        /* @var $repository \Doctrine\Bundle\DoctrineBundle\Repository */
        $qb = $repository->createQueryBuilder('o'); /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb->andWhere('o.isDeleted = 0');
        $products = $qb->getQuery()->getArrayResult();
        return $this->json($products);
    }


    /**
     * @Route("/products/{id}", name="product_delete", methods={"DELETE"})
     */
    public function remove($id, EntityManagerInterface $em)
    {
        $product = $this->get('doctrine')
            ->getRepository(Product::class)
            ->findOneBy([
                'id' => $id,
                'isDeleted' => 0,
            ]);
        if (!$product) {
            throw new NotFoundHttpException(sprintf('Product %d not found', $id));
        }

        $em
            ->remove($product);
        try {
            $em->flush();
        }
        catch (ORMException $e) {
            throw new HttpException(500, 'Server could not delete this product, please try again');
        }
        return new Response('Product deleted', 200);
    }

}

