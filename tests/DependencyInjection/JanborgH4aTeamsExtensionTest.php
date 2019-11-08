<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_teams
 *
 * (c) Jan Lünborg
 *
 * @license LGPL-3.0-or-later
 */

namespace Janborg\H4aTeams\Tests\DependencyInjection;

use Janborg\H4aTeams\DependencyInjection\JanborgH4aTeamsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class JanborgH4aTeamsExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     **/
    private $container;

    /**
     * {@inheritdoc}
     **/
    protected function setup(): void
    {
        parent::setup();

        $this->container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));

        $extension = new JanborgH4aTeamsExtension();
        $extension->load([], $this->container);
    }
}
