<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin\Bridge;

use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridge;
use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class MercanetBnpParibasBridgeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MercanetBnpParibasBridge::class);
        $this->shouldHaveType(MercanetBnpParibasBridgeInterface::class);
    }

    function let(RequestStack $requestStack)
    {
        $this->beConstructedWith($requestStack);
    }

    function it_is_post_method(
        RequestStack $requestStack,
        Request $request
    )
    {
        $request->isMethod('POST')->willReturn(true);
        $requestStack->getCurrentRequest()->willReturn($request);

        $this->isPostMethod()->shouldReturn(true);
    }

    function it_is_not_post_method(
        RequestStack $requestStack,
        Request $request
    )
    {
        $request->isMethod('POST')->willReturn(false);
        $requestStack->getCurrentRequest()->willReturn($request);

        $this->isPostMethod()->shouldReturn(false);
    }

    function it_creates_mercanet()
    {
        $this->createMercanet('key')->shouldBeAnInstanceOf(Mercanet::class);
    }

    function it_payment_verification_has_been_thrown(
        RequestStack $requestStack,
        Request $request
    )
    {
        $request->isMethod('POST')->willReturn(true);
        $requestStack->getCurrentRequest()->willReturn($request);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('paymentVerification', ['key'])
        ;
    }
}
