<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTeams\Tests;

use Janborg\H4aTeams\JanborgH4aTeamsBundle;
use PHPUnit\Framework\TestCase;

class JanborgH4aTeamsBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new JanborgH4aTeamsBundle();

        $this->assertInstanceOf('Janborg\H4aTeams\JanborgH4aTeamsBundle', $bundle);
    }
}
