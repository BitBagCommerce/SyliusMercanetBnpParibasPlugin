<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Legacy\SimplePayment;
use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;
use Payum\Core\Payum;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    private $api = [];

    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var MercanetBnpParibasBridgeInterface
     */
    private $mercanetBnpParibasBridge;

    /**
     * @param Payum $payum
     * @param MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge
     */
    public function __construct(
        Payum $payum,
        MercanetBnpParibasBridgeInterface $mercanetBnpParibasBridge
    )
    {
        $this->mercanetBnpParibasBridge = $mercanetBnpParibasBridge;
        $this->payum = $payum;
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
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $transactionReference = isset($model['transactionReference']) ? $model['transactionReference'] : null;

        if ($transactionReference !== null) {

            if ($this->mercanetBnpParibasBridge->isPostMethod()) {

                $model['status'] = $this->mercanetBnpParibasBridge->paymentVerification($this->api['secret_key']) ?
                    PaymentInterface::STATE_COMPLETED : PaymentInterface::STATE_CANCELLED;

                $request->setModel($model);

                return;
            }

            if ($model['status'] === PaymentInterface::STATE_COMPLETED) {

                return;
            }
        }

        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $secretKey = $this->api['secret_key'];

        $mercanet = $this->mercanetBnpParibasBridge->createMercanet($secretKey);

        $environment = $this->api['environment'];
        $merchantId = $this->api['merchant_id'];

        $automaticResponseUrl = $notifyToken->getTargetUrl();
        $currencyCode = $payment->getCurrencyCode();
        $targetUrl = $request->getToken()->getTargetUrl();
        $amount = $payment->getAmount();
        $transactionReference = "MercanetWS" . uniqid() . "ORDER" . $payment->getOrder()->getNumber();

        $model['transactionReference'] = $transactionReference;

        $simplePayment = new SimplePayment(
            $mercanet,
            $merchantId,
            $environment,
            $amount,
            $targetUrl,
            $currencyCode,
            $transactionReference,
            $automaticResponseUrl
        );

        $request->setModel($model);
        $simplePayment->execute();
    }

    /**
     * @param string $gatewayName
     * @param object $model
     *
     * @return TokenInterface
     */
    private function createNotifyToken($gatewayName, $model)
    {
        return $this->payum->getTokenFactory()->createNotifyToken(
            $gatewayName,
            $model
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
