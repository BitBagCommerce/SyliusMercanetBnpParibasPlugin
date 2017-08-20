<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\MercanetBnpParibasPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $secretKey
     */
    public function setMercanetBnpParibasPluginGatewaySecretKey($secretKey);

    /**
     * @param string $merchantId
     */
    public function setMercanetBnpParibasPluginGatewayMerchantId($merchantId);

    /**
     * @param string $environment
     */
    public function setMercanetBnpParibasPluginGatewayEnvironment($environment);

    /**
     * @param string $message
     *
     * @return bool
     */
    public function findValidationMessage($message);
}