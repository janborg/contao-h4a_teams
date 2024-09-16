<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Contao\DataContainer;
use Contao\DC_Table;

/*
 * Table tl_h4a_teams_archive
 */

$GLOBALS['TL_DCA']['tl_h4a_teams_archive'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_h4a_teams'],
        'switchToEdit' => true,
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
            'fields' => ['title'],
            'flag' => DataContainer::SORT_ASC,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['title', 'group'],
            'format' => '%s - [%s]',
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
                'href' => 'table=tl_h4a_teams',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
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
            'eval' => ['doNotCopy' => true, 'maxlength' => 255, 'tl_class' => 'w50 clr'],
            'options' => [
                'Männer', 'Frauen', 'Jugend',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
