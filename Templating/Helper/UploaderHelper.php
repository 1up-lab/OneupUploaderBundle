<?php

namespace Oneup\UploaderBundle\Templating\Helper;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\Helper;

class UploaderHelper extends Helper
{
    protected $router;
    
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function getName()
    {
        return 'oneup_uploader';
    }
    
    public function endpoint($key)
    {
        return $this->router->generate(sprintf('_uploader_%s', $key));
    }
}