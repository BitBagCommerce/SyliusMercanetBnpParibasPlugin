<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Action\CaptureAction;
use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use Payum\Core\Model\Token;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Model\Order;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class CaptureActionSpec extends ObjectBehavior
{
    function let(Payum $payum, MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge)
    {
        $this->beConstructedWith($payum, $mercanetBnpParibasBridge);
        $this->setApi([
            'environment' => 'https://payment-webinit-mercanet.test.sips-atos.com/rs-services/v2/paymentInit',
            'secret_key' => '123',
            'merchant_id' => '123'
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CaptureAction::class);
    }

    function it_executes(
        Capture $request,
        \ArrayObject $arrayObject,
        PaymentInterface $payment,
        Token $token,
        Token $notifyToken,
        Payum $payum,
        GenericTokenFactory $genericTokenFactory,
        Order $order,
        MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge,
        Mercanet $mercanet
    )
    {
        $mercanetBnpParibasBridge->createMercanet('123')->willReturn($mercanet);
        $payment->getOrder()->willReturn($order);
        $payment->getCurrencyCode()->willReturn('EUR');
        $payment->getAmount()->willReturn(100);
        $notifyToken->getTargetUrl()->willReturn('url');
        $token->getTargetUrl()->willReturn('url');
        $token->getGatewayName()->willReturn('test');
        $token->getDetails()->willReturn([]);
        $genericTokenFactory->createNotifyToken('test', [])->willReturn($notifyToken);
        $payum->getTokenFactory()->willReturn($genericTokenFactory);
        $request->getModel()->willReturn($arrayObject);
        $request->getFirstModel()->willReturn($payment);
        $request->getToken()->willReturn($token);
        $request->setModel(Argument::any())->shouldBeCalled();

        $this
            ->shouldThrow(HttpResponse::class)
            ->during('execute', [$request])
        ;
    }
}
