<?php
/**
 * @package     Smsreader Mautic Bundle
 * @copyright   2017 Servian Pty Ltd. All rights reserved
 * @author      Ben Evans
 * @link        http://www.servian.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Smsreader',
    'description' => 'Twillio SMS callback Plugin.',
    'version'     => '0.1',
    'author'      => 'Ben Evans',

    'routes'      => [
        'main' => [
            'smsreader'         => [
                'path'       => 'smsreader/config',
                'controller' => 'SmsreaderBundle:Smsreader:index'
            ]
        ],
        'public' => [
            'smsreader_public' => [
                'path' => 'sms/callback',
                'controller' => 'SmsreaderBundle:Public:trigger',
                'defaults' => [
                    'command' => ''
                ]
            ]
        ]
    ],
    'menu'     => [
        'admin' => [
            'items'    => [
                'smsreader.title' => [
                    'id'        => 'smsreader',
                    'route'     => 'smsreader',
                    'iconClass' => 'fa-comments',
                ]
            ]
        ]
    ],

    'services' => [
        'models' =>  [
            'mautic.smsreader.model.smsreader' => [
                'class' => 'MauticPlugin\SmsreaderBundle\Model\SmsreaderModel',
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.configurator',
                    'mautic.helper.cache',
                ]
            ]
        ]
    ],
];
