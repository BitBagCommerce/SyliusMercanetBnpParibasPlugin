<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Form\Extension;

use BitBag\MercanetBnpParibasPlugin\Validator\Constraints\Currency;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class PaymentMethodExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var GatewayConfig $gatewayFactory */
        $gatewayFactory = $options['data']->getGatewayConfig();

        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.payment_method.channels',
                'constraints' => [
                    new Currency([
                        'groups' => 'sylius',
                        'factoryName' => $gatewayFactory->getFactoryName()
                    ]),
                ],
            ])
            ->add('gatewayConfig', GatewayConfigType::class, [
                'label' => false,
                'data' => $gatewayFactory,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentMethodInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if (null === $gatewayConfig->getGatewayName()) {
                    $gatewayConfig->setGatewayName(StringInflector::nameToLowercaseCode($paymentMethod->getName()));
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PaymentMethodType::class;
    }
}