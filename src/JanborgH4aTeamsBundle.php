<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTeams;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JanborgH4aTeamsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
