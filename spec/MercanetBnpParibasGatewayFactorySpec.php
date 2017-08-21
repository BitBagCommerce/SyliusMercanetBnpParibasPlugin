<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin;

use BitBag\MercanetBnpParibasPlugin\MercanetBnpParibasGatewayFactory;
use PhpSpec\ObjectBehavior;
use Payum\Core\GatewayFactory;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class MercanetBnpParibasGatewayFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MercanetBnpParibasGatewayFactory::class);
        $this->shouldHaveType(GatewayFactory::class);
    }

    function it_populateConfig_run()
    {
        $this->createConfig([]);
    }
}
