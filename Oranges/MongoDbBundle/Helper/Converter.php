<?php
namespace Oranges\MongoDbBundle\Helper;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

class Converter implements ParamConverterInterface
{
    function apply(Request $request, ConfigurationInterface $configuration)
    {

        print_r($request);die();
        $entity = $configuration->getClass();

        $entity = new $entity();
        $entity->load($request->attributes->get(
            $configuration->getName()
        ));
        
        $request->attributes->set(
            $configuration->getName(),
            $entity);
    }

    function supports(ConfigurationInterface $configuration)
    {
        if (!$configuration->getClass()) {
            return false;
        }
 
        // for simplicity, everything that has a "class" type hint is supported
 
        return true;
    }
}