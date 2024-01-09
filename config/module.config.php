<?php

namespace BplAdmin;

use Laminas\Router\Http\Segment;
use BplAdmin\Controller\AdminController;
use BplAdmin\Controller\RoleManagementController;
use BplAdmin\Controller\AccessManagement\AccessListController;
use BplAdmin\Controller\UserManagement\AssignRoleController;
use BplAdmin\Controller\UserManagement\CredentialController;
use BplAdmin\Controller\UserManagement\DeleteController;
use BplAdmin\Controller\UserManagement\ListController;
use BplAdmin\Controller\UserManagement\ProfileController;
use BplAdmin\Controller\UserManagement\RegisterController;
use BplAdmin\Factory\Controller\AdminControllerFactory;
use BplAdmin\Factory\Controller\RoleManagementControllerFactory;
use BplAdmin\Factory\Controller\AccessManagement\AccessListControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\AssignRoleControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\CredentialControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\DeleteControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\ListControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\ProfileControllerFactory;
use BplAdmin\Factory\Controller\UserManagement\RegisterControllerFactory;
use BplAdmin\ModuleOpions\CrudOptions;
use BplAdmin\Factory\ModuleOpions\CrudOptionsFactory;
use BplAdmin\ModuleOpions\ExcludeOptions;
use BplAdmin\Factory\ModuleOpions\ExcludeOptionsFactory;
use BplAdmin\ModuleOpions\AccessOptions;
use BplAdmin\Factory\ModuleOpions\AccessOptionsFactory;
use BplAdmin\Service\ControllerAccessManagementService;
use BplAdmin\Factory\Service\ControllerAccessManagementServiceFactory;
use BplAdmin\Service\ControllerGuardConfigManager;
use BplAdmin\Factory\Service\ControllerGuardConfigManagerFactory;
use BplAdmin\Form\AppPermissionForm;
use BplAdmin\Factory\Form\AppPermissionFormFactory;
use BplAdmin\Service\ResourceGuardConfigManager;
use BplAdmin\Factory\Service\ResourceGuardConfigManagerFactory;

return [
    'controllers' => [
        'factories' => [
            AdminController::class => AdminControllerFactory::class,
            RoleManagementController::class => RoleManagementControllerFactory::class,
            ListController::class => ListControllerFactory::class,
            RegisterController::class => RegisterControllerFactory::class,
            ProfileController::class => ProfileControllerFactory::class,
            AssignRoleController::class => AssignRoleControllerFactory::class,
            CredentialController::class => CredentialControllerFactory::class,
            DeleteController::class => DeleteControllerFactory::class,
            AccessListController::class => AccessListControllerFactory::class
        ],
    ],
    'service_manager' => [
        'factories' => [
            CrudOptions::class => CrudOptionsFactory::class,
            ExcludeOptions::class => ExcludeOptionsFactory::class,
            AccessOptions::class => AccessOptionsFactory::class,
            ControllerAccessManagementService::class => ControllerAccessManagementServiceFactory::class,
            ControllerGuardConfigManager::class => ControllerGuardConfigManagerFactory::class,
            ResourceGuardConfigManager::class => ResourceGuardConfigManagerFactory::class,
            AppPermissionForm::class => AppPermissionFormFactory::class,
        ],
    ],
    'bpl_admin' => [
        'crud' => [
            'items_per_page' => 5,
        ],
        'acl' => [
            'acl_filename' => 'bpladmin.acl.local.php',
            'acl_file_location' => __DIR__.'/../../../../config/autoload',
        ],
    ],
    'router' => [
        'routes' => [
            'bpl-admin' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => AdminController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'dashboard' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/dashboard',
                            'defaults' => [
                                'controller' => AdminController::class,
                            ],
                        ],
                    ],
                    'role-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/role-management[/:action][/:id]',
                            'defaults' => [
                                'controller' => RoleManagementController::class,
                            ],
                        ],
                    ],
                    'user-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/user-management',
                            'defaults' => [
                                'controller' => ListController::class,
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'list' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/list[/:pageNo]',
                                    'defaults' => [
                                        'controller' => ListController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'register' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/register',
                                    'defaults' => [
                                        'controller' => RegisterController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'assign-roles' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/assign-roles/[:user_id]',
                                    'defaults' => [
                                        'controller' => AssignRoleController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'change-profile' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/change-profile/[:user_id]',
                                    'defaults' => [
                                        'controller' => ProfileController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'change-password' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/change-password/[:user_id]',
                                    'defaults' => [
                                        'controller' => CredentialController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/delete/[:user_id]',
                                    'defaults' => [
                                        'controller' => DeleteController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'access-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/access-management[/:action][/:identifier][/:roleName]',
                            'defaults' => [
                                'controller' => AccessListController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'circlical' => [
        'user' => [
            'guards' => [
                'bpl-admin' => [
                    'controllers' => [
                        AdminController::class => [
                            'default' => ['administrator'],
                        ],
                        RoleManagementController::class => [
                            'default' => ['administrator'],
                        ],
                        ListController::class => [
                            'default' => ['administrator'],
                        ],
                        RegisterController::class => [
                            'default' => ['administrator'],
                        ],
                        AssignRoleController::class => [
                            'default' => ['administrator'],
                        ],
                        ProfileController::class => [
                            'default' => ['administrator'],
                        ],
                        CredentialController::class => [
                            'default' => ['administrator'],
                        ],
                        DeleteController::class => [
                            'default' => ['administrator'],
                        ],
                        AccessListController::class => [
                            'default' => ['administrator'],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'bpl-admin' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'bpl-admin/pagination' => __DIR__ . '/../view/partial/pagination.phtml',
            'bpl-admin/form' => __DIR__ . '/../view/partial/form.phtml',
        ],
    ],
];
