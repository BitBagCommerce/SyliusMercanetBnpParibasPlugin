<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\MercanetBnpParibasPlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function setMercanetBnpParibasPluginGatewaySecretKey($secretKey)
    {
        $this->getDocument()->fillField('Secure key', $secretKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setMercanetBnpParibasPluginGatewayMerchantId($merchantId)
    {
        $this->getDocument()->fillField('Merchant ID', $merchantId);
    }

    /**
     * {@inheritdoc}
     */
    public function setMercanetBnpParibasPluginGatewayEnvironment($environment)
    {
        $this->getDocument()->selectFieldOption('Environment', $environment);
    }

    /**
     * {@inheritdoc}
     */
    public function findValidationMessage($message)
    {
        $elements = $this->getDocument()->findAll('css', '.sylius-validation-error');

        /** @var NodeElement $element */
        foreach ($elements as $element) {
            if ($element->getText() === $message) {
                return true;
            }
        }

        return false;
    }
}