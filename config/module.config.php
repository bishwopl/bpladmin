<?php

namespace BplAdmin;

use Laminas\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
            Controller\RoleManagementController::class => Controller\Factory\RoleManagementControllerFactory::class,
            Controller\UserManagement\ListController::class => Controller\Factory\UserManagement\ListControllerFactory::class,
            Controller\UserManagement\RegisterController::class => Controller\Factory\UserManagement\RegisterControllerFactory::class,
            Controller\UserManagement\ProfileController::class => Controller\Factory\UserManagement\ProfileControllerFactory::class,
            Controller\UserManagement\AssignRoleController::class => Controller\Factory\UserManagement\AssignRoleControllerFactory::class,
            Controller\UserManagement\CredentialController::class => Controller\Factory\UserManagement\CredentialControllerFactory::class,
            Controller\UserManagement\DeleteController::class => Controller\Factory\UserManagement\DeleteControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOpions\CrudOptions::class => ModuleOpions\CrudOptionsFactory::class,
            Service\UserService::class => Service\Factory\UserServiceFactory::class
        ],
    ],
    'bpl_admin' => [
        'crud' => [
            'items_per_page' => 2,
        ],
    ],
    'router' => [
        'routes' => [
            'bpl-admin' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'role-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/role-management[/:action][/:id1]',
                            'defaults' => [
                                'controller' => Controller\RoleManagementController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'user-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/user-management',
                            'defaults' => [
                                'controller' => Controller\UserManagement\ListController::class,
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
                                        'controller' => Controller\UserManagement\ListController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'register' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/register',
                                    'defaults' => [
                                        'controller' => Controller\UserManagement\RegisterController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'assign-roles' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/assign-roles/[:user_id]',
                                    'defaults' => [
                                        'controller' => Controller\UserManagement\AssignRoleController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'change-profile' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/change-profile/[:user_id]',
                                    'defaults' => [
                                        'controller' => Controller\UserManagement\ProfileController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'change-password' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/change-password/[:user_id]',
                                    'defaults' => [
                                        'controller' => Controller\UserManagement\CredentialController::class,
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/delete/[:user_id]',
                                    'defaults' => [
                                        'controller' => Controller\UserManagement\DeleteController::class,
                                        'action' => 'index',
                                    ],
                                ],
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
                'BplUser' => [
                    'controllers' => [
                        Controller\AdminController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\RoleManagementController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\ListController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\RegisterController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\AssignRoleController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\ProfileController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\CredentialController::class => [
                            'default' => ['administrator'],
                        ],
                        Controller\UserManagement\DeleteController::class => [
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
            'bpl-admin/pagination' => __DIR__ . '/../view/bpl-admin/partial/pagination.phtml',
        ],
    ],
];
