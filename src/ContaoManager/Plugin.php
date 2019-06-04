<?php

/*
 * This file is part of contao-h4a_teams
 *
 * (c) Jan Lünborg
 *
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTeams\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Janborg\H4aTeams\JanborgH4aTeamsBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(JanborgH4aTeamsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
