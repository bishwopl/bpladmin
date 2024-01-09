<?php

declare(strict_types=1);

namespace BplAdmin\Factory\Service;

use BplAdmin\Service\ResourceGuardConfigManager;
use CirclicalUser\Mapper\RoleMapper;
use CirclicalUser\Service\AccessService;
use CirclicalUser\Mapper\GroupPermissionMapper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ResourceGuardConfigManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ControllerGuardConfigManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /*@var $grpPerMapper GroupPermissionMapper */
        $config = $container->get('Config');
        $roleMapperKey = $config['circlical']['user']['providers']['role'] ?? RoleMapper::class;
        $groupMapperKey = $config['circlical']['user']['providers']['rules']['group'] ?? GroupPermissionMapper::class;

        $resourceActionList = [];
        $apiConfigs = isset($config['api-tools-rest'])?$config['api-tools-rest']:[];
        foreach($apiConfigs as $resourceName => $configuration){
            if(isset($configuration['entity_http_methods'])){
                $resourceActionList[$resourceName.'::entity'] = $configuration['entity_http_methods'];
            }
            if(isset($configuration['collection_http_methods'])){
                $resourceActionList[$resourceName.'::collection'] = $configuration['collection_http_methods'];
            }
        }
        
        $configFromBplAdmin = isset($config['bpl_admin']['resource-action-list'])?$config['bpl_admin']['resource-action-list']:[];
        foreach($configFromBplAdmin as $resourceName => $actions){
            $resourceActionList[$resourceName] = $actions;
        }

        return new ResourceGuardConfigManager(
                $container->get(AccessService::class),
                $container->get($roleMapperKey),
                $container->get($groupMapperKey),
                $resourceActionList
        );
    }
}
