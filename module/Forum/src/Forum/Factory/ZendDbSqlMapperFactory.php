<?php
namespace Forum\Factory;

use Forum\Model\Page;
use Forum\Model\User;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Forum\Mapper\ZendDbSqlMapper;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;

class ZendDbSqlMapperFactory implements  FactoryInterface{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $classmethod=new ClassMethods(false);
        $classmethod->addFilter("arraycoppy",
         new MethodMatchFilter("getArrayCopy"),
         FilterComposite::CONDITION_AND
        );
        $classmethod->addFilter("user",
            new MethodMatchFilter("getUser"),
            FilterComposite::CONDITION_AND
        );
        $prototypeArr=new \ArrayObject();
        $prototypeArr->append(new User());
        return new ZendDbSqlMapper(
            $serviceLocator->get('Zend\Db\Adapter\Adapter'),
            $classmethod,
            new Page(),
            $prototypeArr
        );
    }
}