<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Bridge;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class MercanetBnpParibasBridge implements MercanetBnpParibasBridgeInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function createMercanet($secretKey)
    {
        return new Mercanet($secretKey);
    }

    /**
     * {@inheritDoc}
     */
    public function paymentVerification($secretKey)
    {
        if ($this->isPostMethod()) {

            $paymentResponse = new Mercanet($secretKey);
            $paymentResponse->setResponse($_POST);

            return $paymentResponse->isValid();
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isPostMethod()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest->isMethod('POST');
    }
}