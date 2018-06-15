<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\MercanetBnpParibasPlugin\Behat\Mocker;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use BitBag\MercanetBnpParibasPlugin\Bridge\MercanetBnpParibasBridgeInterface;
use Sylius\Behat\Service\Mocker\Mocker;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class MercanetBnpParibasMocker
{
    /**
     * @var Mocker
     */
    private $mocker;

    /**
     * @param Mocker $mocker
     */
    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    /**
     * @param callable $action
     */
    public function completedPayment(callable $action)
    {
        $openMercanetBnpParibasWrapper = $this->mocker
            ->mockService('bitbag.mercanet_bnp_paribas.bridge.mercanet_bnp_paribas_bridge', MercanetBnpParibasBridgeInterface::class);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('createMercanet')
            ->andReturn(new Mercanet('test'));

        $openMercanetBnpParibasWrapper
            ->shouldReceive('paymentVerification')
            ->andReturn(true);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('isPostMethod')
            ->andReturn(true);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('setSecretKey', 'setEnvironment', 'setMerchantId')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getSecretKey')
            ->andReturn('test')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getMerchantId')
            ->andReturn('test')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getEnvironment')
            ->andReturn(Mercanet::TEST)
        ;

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function canceledPayment(callable $action)
    {
        $openMercanetBnpParibasWrapper = $this->mocker
            ->mockService('bitbag.mercanet_bnp_paribas.bridge.mercanet_bnp_paribas_bridge', MercanetBnpParibasBridgeInterface::class);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('createMercanet')
            ->andReturn(new Mercanet('test'));

        $openMercanetBnpParibasWrapper
            ->shouldReceive('paymentVerification')
            ->andReturn(false);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('isPostMethod')
            ->andReturn(true);

        $openMercanetBnpParibasWrapper
            ->shouldReceive('setSecretKey', 'setEnvironment', 'setMerchantId')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getSecretKey')
            ->andReturn('test')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getMerchantId')
            ->andReturn('test')
        ;

        $openMercanetBnpParibasWrapper
            ->shouldReceive('getEnvironment')
            ->andReturn(Mercanet::TEST)
        ;

        $action();

        $this->mocker->unmockAll();
    }
}