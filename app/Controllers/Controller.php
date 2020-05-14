<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;

abstract class Controller {

    protected $c;
    
    public function __construct(ContainerInterface $c) {
        $this->c = $c;
    }
}