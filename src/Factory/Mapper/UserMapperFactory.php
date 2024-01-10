<?php

namespace BplAdmin\Factory\Mapper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use BplAdmin\Mapper\UserMapper as BplAdminUserMapper;
use CirclicalUser\Mapper\UserMapper as CirclicalUserMapper;
use BplUserMongoDbODM\Mapper\UserMapper as BplUserMongoMapper;
use BplUserMongoDbODM\Document\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ODM\MongoDB\DocumentManager;

class UserMapperFactory implements FactoryInterface {

    /**
     * Create ModuleOptions Service
     * 
     * @param ContainerInterface $container
     * @param type $requestedName
     * @param mixed $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $config = $container->get('Config');
        $userConfig = $config['circlical']['user'];
        $objectClassName = $userConfig['doctrine']['entity'];
        $chosenMapper = $userConfig['providers']['user'] ?? CirclicalUserMapper::class;
        
        if($chosenMapper == CirclicalUserMapper::class){
            return new BplAdminUserMapper($container->get(EntityManager::class), $objectClassName);
        }elseif($chosenMapper == BplUserMongoMapper::class){
            return new BplAdminUserMapper($container->get(DocumentManager::class), User::class);
        }
        throw new \Exception('Please check your configuration !');
    }

}