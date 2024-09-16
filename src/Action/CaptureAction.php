<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Action;

use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use BitBag\MercanetBnpParibasPlugin\Legacy\SimplePayment;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Payum;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /** @var Payum */
    private $payum;

    /** @var MercanetBnpParibasBridgeInterface */
    private $mercanetBnpParibasBridge;

    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    public function setApi($mercanetBnpParibasBridge): void
    {
        if (!$mercanetBnpParibasBridge instanceof MercanetBnpParibasBridgeInterface) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->mercanetBnpParibasBridge = $mercanetBnpParibasBridge;
    }

    /**
     * @inheritDoc
     *
     * @param Capture $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $transactionReference = $model['transactionReference'] ?? null;

        if (null !== $transactionReference) {
            if ($this->mercanetBnpParibasBridge->isPostMethod()) {
                $model['status'] = $this->mercanetBnpParibasBridge->paymentVerification() ?
                    PaymentInterface::STATE_COMPLETED : PaymentInterface::STATE_CANCELLED
                ;

                if ($model['status'] == PaymentInterface::STATE_COMPLETED) {
                    $model['authorisationId'] = $this->mercanetBnpParibasBridge->getAuthorisationId();
                }

                $request->setModel($model);

                return;
            }

            if (PaymentInterface::STATE_COMPLETED === $model['status']) {
                return;
            }
        }

        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $secretKey = $this->mercanetBnpParibasBridge->getSecretKey();

        $mercanet = $this->mercanetBnpParibasBridge->createMercanet($secretKey);

        $environment = $this->mercanetBnpParibasBridge->getEnvironment();
        $merchantId = $this->mercanetBnpParibasBridge->getMerchantId();
        $keyVersion = $this->mercanetBnpParibasBridge->getKeyVersion();

        $automaticResponseUrl = $notifyToken->getTargetUrl();
        /** @var string $currencyCode */
        $currencyCode = $payment->getCurrencyCode();
        /** @phpstan-ignore-next-line We should not change that now*/
        $targetUrl = $request->getToken()->getTargetUrl();
        /** @var int $amount */
        $amount = $payment->getAmount();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $transactionReference = 'MercanetWS' . uniqid() . 'OR' . $order->getNumber();

        $model['transactionReference'] = $transactionReference;

        $simplePayment = new SimplePayment(
            $mercanet,
            $merchantId,
            $keyVersion,
            $environment,
            $amount,
            $targetUrl,
            $currencyCode,
            $transactionReference,
            $automaticResponseUrl,
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
            $model,
        );
    }

    /**
     * @inheritDoc
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
