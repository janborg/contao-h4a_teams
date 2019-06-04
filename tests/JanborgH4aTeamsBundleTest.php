<?php

/*
 * This file is part of contao-h4a_teams
 *
 * (c) Jan Lünborg
 *
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTeams\Tests;

use Janborg\H4aTeams\JanborgH4aTeamsBundle;
use PHPUnit\Framework\TestCase;

class JanborgH4aTeamsBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new JanborgH4aTeamsBundle();

        $this->assertInstanceOf('Janborg\H4aTeams\JanborgH4aTeamsBundle', $bundle);
    }
}
