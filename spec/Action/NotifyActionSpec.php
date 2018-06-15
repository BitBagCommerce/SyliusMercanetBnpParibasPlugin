<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Action\NotifyAction;
use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use Payum\Core\Request\Notify;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class NotifyActionSpec extends ObjectBehavior
{
    function let(
        MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge,
        FactoryInterface $stateMachineFactory
    ) {
        $this->beConstructedWith($stateMachineFactory);
        $this->setApi($mercanetBnpParibasBridge);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NotifyAction::class);
    }

    function it_execute(
        Notify $request,
        \ArrayObject $arrayObject,
        MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge,
        PaymentInterface $payment,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ) {
        $request->getModel()->willReturn($arrayObject);
        $request->getFirstModel()->willReturn($payment);
        $mercanetBnpParibasBridge->isPostMethod()->willReturn(true);
        $mercanetBnpParibasBridge->paymentVerification()->willReturn(true);
        $stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->willReturn($stateMachine);

        $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->execute($request);
    }
}
