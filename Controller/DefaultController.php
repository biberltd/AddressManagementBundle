<?php

namespace BiberLtd\Bundle\AddressManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BiberLtdAddressManagementBundle:Default:index.html.twig', array('name' => $name));
    }
}
