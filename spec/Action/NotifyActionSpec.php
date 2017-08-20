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
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class NotifyActionSpec extends ObjectBehavior
{
    function let(MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge)
    {
        $this->beConstructedWith($mercanetBnpParibasBridge);
        $this->setApi(['environment' => 'https://payment-webinit-mercanet.test.sips-atos.com/rs-services/v2/paymentInit', 'secret_key' => '123', 'merchant_id' => '123']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NotifyAction::class);
    }

    function it_execute(
        Notify $request,
        \ArrayObject $arrayObject,
        MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge,
        PaymentInterface $payment
    )
    {
        $payment->setState(PaymentInterface::STATE_COMPLETED)->shouldBeCalled();

        $request->getModel()->willReturn($arrayObject);
        $request->getFirstModel()->willReturn($payment);

        $mercanetBnpParibasBridge->isPostMethod()->willReturn(true);
        $mercanetBnpParibasBridge->paymentVerification(123)->willReturn(true);

        $this->execute($request);
    }
}
