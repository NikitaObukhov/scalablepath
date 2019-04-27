<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class GenerateProductsCommand extends Command implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    protected static $defaultName = 'app:generate-products';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('product-number', InputArgument::REQUIRED, 'Number of products')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('product-number');
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        for ($i = 0; $i < $arg1; $i++) {
            $product = new Product();
            $product->setName(sprintf('Product %d', $i));
            $product->setDescription('A sample description');
            $product->setPrice(mt_rand(100, 500));
            $em->persist($product);
        }
        $em->flush();


        $io->success(sprintf('You have generated %d products.', $arg1));
    }
}
