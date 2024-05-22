<?php

declare(strict_types=1);

use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\System;
use Contao\DC_Table;

/*
 * Table tl_h4a_teams
 */

$GLOBALS['TL_DCA']['tl_h4a_teams'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_h4a_teams_archive',
        'enableVersioning' => true,
        'switchToEdit'                => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['h4aSaison DESC'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['h4aSaison', 'name'],
            'format' => '%s %s',
        ],
        'operations' => [
            'edit',
            'copy',
            'cut',
            'delete',
            'toggle' => [
                'href'                => 'act=toggle&amp;field=published',
                'icon'                => 'visible.svg',
                'showInHeader'        => true
            ],
            'show',
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['overwriteMeta'],
        'default' => '{title_legend}, h4aSaison, name, alias;
        {image_legend},singleSRC,fullsize,size,floating,overwriteMeta,teamImageDescription;
        {trainer_legend},trainer_name,trainer_email;
        {trainingszeiten_legend},trainingszeiten',
    ],

    // Subpalettes
    'subpalettes' => [
        'overwriteMeta' => 'alt, imageTitle, imageUrl, caption',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_h4aseason.title',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'h4aSaison' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_h4a_seasons.season',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'chosen' => true,
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'name' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(128) BINARY NOT NULL default ''",
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
            'sql' => 'binary(16) NULL',
        ],
        'size' => [
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
            'options_callback' => static fn () => System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance()),
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'floating' => [
            'exclude' => true,
            'inputType' => 'radioTable',
            'options' => ['above', 'left', 'right', 'below'],
            'eval' => ['cols' => 4, 'tl_class' => 'w50'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'sql' => "varchar(32) NOT NULL default 'above'",
        ],
        'fullsize' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'm12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'overwriteMeta' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'alt' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'imageTitle' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'imageUrl' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'teamImageDescription' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'trainer_name' => [
            'exclude'                 => true,
            'sorting'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'trainer_email' => [
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => [
                'mandatory' => false,
                'rgxp' => 'email',
                'maxlength' => 255,
                'decodeEntities' => true,
                'tl_class' => 'w50'
            ],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'trainingszeiten' => [
            'exclude' => false,
            'inputType' => 'group',
            'palette' => ['wochentag', 'halle', 'trainingStart', 'trainingEnd'],
            'fields' => [
                'wochentag' => [
                    'inputType' => 'select',
                    'options' => ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'],
                    'eval' => [
                        'mandatory' => true,
                        'tl_class' => 'w50',
                        'includeBlankOption' => true,
                        'chosen' => true,
                    ],
                ],
                'trainingStart' => [
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => 'time',
                        'mandatory' => true,
                        'doNotCopy' => true,
                        'tl_class' => 'w50'
                    ],
                    //                    'sql'   => "bigint(20) NULL"
                ],
                'trainingEnd' => [
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => 'time',
                        'mandatory' => true,
                        'doNotCopy' => true,
                        'tl_class' => 'w50'
                    ],
                    //                    'sql'   => "bigint(20) NULL"
                ],
                'halle' => [
                    'inputType' => 'text',
                    'eval' => [
                        'mandatory' => true,
                        'maxlength' => 255,
                        'tl_class' => 'w50',
                    ],
                ],
            ],
            'sql' => 'blob NULL',
        ],
        'h4a_team' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 7,
                'tl_class' => 'w50',
            ],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ],
        'h4a_liga' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 6,
                'tl_class' => 'w50',
            ],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ],
        'my_team_name' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
    ],
];
