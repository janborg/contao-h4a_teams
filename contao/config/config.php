<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

use Janborg\H4aTeams\Model\H4aTeamsArchiveModel;
use Janborg\H4aTeams\Model\H4aTeamsModel;

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

$GLOBALS['BE_MOD']['content']['teams']['tables'] = ['tl_h4a_teams_archive', 'tl_h4a_teams'];

// Register Models
$GLOBALS['TL_MODELS']['tl_h4a_teams_archive'] = H4aTeamsArchiveModel::class;
$GLOBALS['TL_MODELS']['tl_h4a_teams'] = H4aTeamsModel::class;
