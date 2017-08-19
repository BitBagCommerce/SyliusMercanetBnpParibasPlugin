<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use BitBag\MercanetBnpParibasPlugin\OpenMercanetBnpParibasWrapperInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\Notify;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    private $api = [];

    /**
     * @var OpenMercanetBnpParibasWrapperInterface
     */
    private $openMercanetBnpParibasWrapper;

    /**.
     * @param OpenMercanetBnpParibasWrapperInterface $openMercanetBnpParibasWrapper
     */
    public function __construct(OpenMercanetBnpParibasWrapperInterface $openMercanetBnpParibasWrapper)
    {
        $this->openMercanetBnpParibasWrapper = $openMercanetBnpParibasWrapper;
    }

    /**
     * @param  $request
     *
     * @throws \Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);

        if ($this->openMercanetBnpParibasWrapper->paymentVerification($this->api['secret_key'])) {

            /** @var PaymentInterface $payment */
            $payment = $request->getFirstModel();
            Assert::isInstanceOf($payment, PaymentInterface::class);

            $payment->setState(PaymentInterface::STATE_COMPLETED);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
        ;
    }
}