<?php

use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\widgets\Menu;

?>
<aside class="main-sidebar">
    <section class="sidebar">

        <?php
        $menu = [
            ['label' => Yii::t('back', 'Content'), 'options' => ['class' => 'header']],
            ['label' => '<span class="fa fa-cubes"></span> Offered services <span class="caret"></span>', 'url' => '#', 'visible' => true, 'items' => [
                [
                    'label' => '<span class="fa fa-folder-open"></span> Categories',
                    'url' => ['/categories/index'],
                    'visible' => true
                ],
                ['label' => '<span class="fa fa-television"></span> Shows', 'url' => ['/shows/index'], 'visible' => true],
                ['label' => '<span class="fa fa-fort-awesome"></span> Attractions', 'url' => ['/attractions/index'], 'visible' => true],
                ['label' => '<span class="fa fa-cutlery"></span> Hotels', 'url' => ['/hotels/index'], 'visible' => true],
            ]],
            [
                'label'   => '<span class="fa fa-envelope"></span> Feedback <span class="caret"></span>',
                'url'     => '#',
                'visible' => true,
                'items'   => [
                    [
                        'label'   => '<span class="fa fa-envelope"></span> Feedback Messages',
                        'url'     => ['/feedback/index'],
                        'visible' => true
                    ],
                    [
                        'label'   => '<span class="fa fa-list-alt"></span> Feedback Subjects',
                        'url'     => ['/feedback-subject/index'],
                        'visible' => true
                    ],
                    [
                        'label'   => '<span class="fa fa-cog"></span> Feedback Settings',
                        'url'     => ['/feedback-settings/index'],
                        'visible' => true
                    ],
                ]
            ],
            [
                'label'   => '<span class="fa fa-file-word-o"></span> Static pages',
                'url'     => ['/static-page/index'],
                'visible' => true,
            ],
            ['label' => Yii::t('back', 'Settings'), 'options' => ['class' => 'header']],
            [
                'label' => '<span class="fa fa-dashboard"></span> ' . Yii::t('back', 'Change own password'),
                'url'   => ['/user-management/auth/change-own-password']
            ],
            [
                'label' => '<span class="glyphicon glyphicon-lock"></span> ' . Yii::t('back', 'Logout'),
                'url'   => ['/user-management/auth/logout']
            ],
        ];

        if (User::hasRole(['Admin'])) {
            $menu[] = ['label' => Yii::t('back', 'Settings User'), 'options' => ['class' => 'header']];
            if (User::hasRole(['Superadmin'])) {
                $umm = UserManagementModule::menuItems();
            } else {
                $umm[] = [
                    'label'   => '<span class="fa fa-angle-double-right"></span> Users',
                    'url'     => ['/user-management/user/index'],
                    'visible' => true
                ];
            }
            $menu = array_merge($menu, $umm);
        }
        ?>

        <?= Menu::widget(
            [
                'encodeLabels'    => false,
                //'activateItems' => true,
                'activateParents' => true,
                'options'         => ['class' => 'sidebar-menu'],
                'submenuTemplate' => "\n<ul class='treeview-menu'>\n{items}\n</ul>\n",
                'items'           => $menu,
            ]
        ) ?>
    </section>
</aside>
