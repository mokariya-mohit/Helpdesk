<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * This file is loaded in the context of the `Application` class.
  * So you can use  `$this` to reference the application class instance
  * if required.
 */
return function (RouteBuilder $routes): void {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Tasks', 'action' => 'index']);
        $builder->connect('/tasks', ['controller' => 'Tasks', 'action' => 'index']);
        $builder->connect('/daily-updates', ['controller' => 'DailyUpdates', 'action' => 'index']);
        $builder->connect('/helpdesk', ['controller' => 'DailyUpdates', 'action' => 'index']);

        // Team Messaging Routes (Using /messages to avoid InfinityFree mod_security 403 keyword block on 'chat')
        $builder->connect('/messages', ['controller' => 'Chats', 'action' => 'index']);
        $builder->connect('/messages/search-users', ['controller' => 'Chats', 'action' => 'searchUsers']);
        $builder->connect('/messages/send-request', ['controller' => 'Chats', 'action' => 'sendRequest']);
        $builder->connect('/messages/get-requests', ['controller' => 'Chats', 'action' => 'getRequests']);
        $builder->connect('/messages/accept-request', ['controller' => 'Chats', 'action' => 'acceptRequest']);
        $builder->connect('/messages/reject-request', ['controller' => 'Chats', 'action' => 'rejectRequest']);
        $builder->connect('/messages/get-updates', ['controller' => 'Chats', 'action' => 'getUpdates']);
        $builder->connect('/messages/get-list', ['controller' => 'Chats', 'action' => 'getMessages']);
        $builder->connect('/messages/post-msg', ['controller' => 'Chats', 'action' => 'sendMessage']);
        $builder->connect('/messages/delete-msg', ['controller' => 'Chats', 'action' => 'deleteMessage']);
        $builder->connect('/messages/edit-msg', ['controller' => 'Chats', 'action' => 'editMessage']);
        $builder->connect('/messages/mark-read', ['controller' => 'Chats', 'action' => 'markRead']);
        $builder->connect('/messages/get-badge', ['controller' => 'Chats', 'action' => 'getGlobalBadge']);
        $builder->connect('/messages/toggle-reaction', ['controller' => 'Chats', 'action' => 'toggleReaction']);
        $builder->connect('/messages/clear-history', ['controller' => 'Chats', 'action' => 'clearChat']);
        $builder->connect('/messages/create-group', ['controller' => 'Chats', 'action' => 'createGroup']);
        $builder->connect('/messages/get-teammates', ['controller' => 'Chats', 'action' => 'getTeammatesForGroup']);
        $builder->connect('/messages/add-group-members', ['controller' => 'Chats', 'action' => 'addGroupMembers']);
        $builder->connect('/messages/leave-group', ['controller' => 'Chats', 'action' => 'leaveGroup']);
        $builder->connect('/messages/set-typing', ['controller' => 'Chats', 'action' => 'setTyping']);

        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/signup', ['controller' => 'Users', 'action' => 'signup']);
        $builder->connect('/register', ['controller' => 'Users', 'action' => 'signup']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/profile', ['controller' => 'Users', 'action' => 'profile']);
        $builder->connect('/users/get-profile', ['controller' => 'Users', 'action' => 'getProfile']);
        $builder->connect('/users/update-profile', ['controller' => 'Users', 'action' => 'updateProfile']);
        $builder->connect('/users/save-settings', ['controller' => 'Users', 'action' => 'saveSettings']);
        $builder->connect('/users/google-login', ['controller' => 'Users', 'action' => 'googleLogin']);
        $builder->connect('/users/google-auth', ['controller' => 'Users', 'action' => 'googleAuth']);
        $builder->connect('/users/google-callback', ['controller' => 'Users', 'action' => 'googleCallback']);
        $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/users/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/users/verify-reset-otp', ['controller' => 'Users', 'action' => 'verifyResetOtp']);
        $builder->connect('/users/reset-password', ['controller' => 'Users', 'action' => 'resetPassword']);

        $builder->connect('/pages/*', 'Pages::display');

        /*
         * Connect catchall sub-routes
         */
        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
