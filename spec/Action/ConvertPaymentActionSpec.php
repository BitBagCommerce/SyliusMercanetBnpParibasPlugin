<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Action\ConvertPaymentAction;
use PhpSpec\ObjectBehavior;
use Payum\Core\Request\Convert;
use Payum\Core\Model\PaymentInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_execute(
        Convert $request,
        \ArrayObject $arrayObject,
        PaymentInterface $payment
    )
    {
        $request->setResult([])->willReturn($arrayObject);
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $this->execute($request);
    }
}
