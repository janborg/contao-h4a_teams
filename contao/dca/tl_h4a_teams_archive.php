<?php

declare(strict_types=1);

use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\System;
use Contao\DC_Table;

/*
 * Table tl_h4a_teams_archive
 */

$GLOBALS['TL_DCA']['tl_h4a_teams_archive'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_h4a_teams'],
        'switchToEdit'                => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['title DESC'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend}, title, group;',
    ],

    // Subpalettes
    'subpalettes' => [],

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
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams_archive']['title'],
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'group' => [
            'label' => &$GLOBALS['TL_LANG']['tl_h4a_teams_archive']['group'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 128, 'tl_class' => 'w50 clr'],
            'options' => [
                'Männer', 'Frauen', 'Jugend'
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
