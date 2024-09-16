<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

/*
 * Table tl_h4a_teams
 */

$GLOBALS['TL_DCA']['tl_h4a_teams'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_h4a_teams_archive',
        'enableVersioning' => true,
        'switchToEdit' => true,
        'markAsCopy' => 'name',
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
            'fields' => ['h4aSaison'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['h4aSaison', 'name'],
            'format' => '%s %s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'showInHeader' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['overwriteMeta'],
        'default' => '{title_legend}, h4aSaison, name, alias;
                        {image_legend},singleSRC,fullsize,size,floating,overwriteMeta,teamImageDescription;
                        {h4a_legend},h4a_liga,h4a_team,h4a_team_name;
                        {trainer_legend},trainerName,trainerEmail;
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
            'foreignKey' => 'tl_h4a_teams_archive.id',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'h4aSaison' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['h4aSaison'],
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
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['name'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['alias'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(128) BINARY NOT NULL default ''",
        ],
        'singleSRC' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['sinleSRC'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
            'sql' => 'binary(16) NULL',
        ],
        'size' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['size'],
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
            'options_callback' => static fn () => System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance()),
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'floating' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['floating'],
            'exclude' => true,
            'inputType' => 'radioTable',
            'options' => ['above', 'left', 'right', 'below'],
            'eval' => ['cols' => 4, 'tl_class' => 'w50'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'sql' => "varchar(32) NOT NULL default 'above'",
        ],
        'fullsize' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['fullsize'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'm12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'overwriteMeta' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['overwriteMeta'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'alt' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['alt'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'imageTitle' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['imageTitle'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'imageUrl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['imageUrl'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'teamImageDescription' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['teamImageDescription'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'trainerName' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['trainerName'],
            'exclude' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'trainerEmail' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['trainerEmail'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => false,
                'rgxp' => 'email',
                'maxlength' => 255,
                'decodeEntities' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'trainingszeiten' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['trainingszeiten'],
            'exclude' => false,
            'inputType' => 'group',
            'palette' => ['wochentag', 'halle', 'trainingStart', 'trainingEnd'],
            'fields' => [
                'wochentag' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['wohentag'],
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
                    'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['trainingStart'],
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => 'time',
                        'mandatory' => true,
                        'doNotCopy' => true,
                        'tl_class' => 'w50',
                    ],
                    //                    'sql'   => "bigint(20) NULL"
                ],
                'trainingEnd' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['trainingEnd'],
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => 'time',
                        'mandatory' => true,
                        'doNotCopy' => true,
                        'tl_class' => 'w50',
                    ],
                    //                    'sql'   => "bigint(20) NULL"
                ],
                'halle' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['halle'],
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
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['h4a_team'],
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 7,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'h4a_liga' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['h4a_liga'],
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'maxlength' => 6,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'h4a_team_name' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams']['my_team_name'],
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
