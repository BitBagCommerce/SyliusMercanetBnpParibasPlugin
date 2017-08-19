<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin;

use BitBag\MercanetBnpParibasPlugin\Action\ConvertPaymentAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class MercanetBnpParibasGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'mercanet_bnp_paribas',
            'payum.factory_title' => 'Mercanet BNP Paribas',

            'payum.action.convert' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => '',
                'secure_key' => '',
                'merchant_id' => '',
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['secret_key', 'environment', 'merchant_id'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $MercanetConfig = [
                    'secret_key' => $config['secret_key'],
                    'merchant_id' => $config['merchant_id'],
                    'environment' => $config['environment'],
                ];

                return $MercanetConfig;
            };
        }
    }
}
