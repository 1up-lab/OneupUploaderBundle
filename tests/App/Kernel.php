<?php

declare(strict_types=1);

use Oneup\UploaderBundle\OneupUploaderBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new SensioFrameworkExtraBundle(),

            // bundle to test
            new OneupUploaderBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/config/config.yml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
