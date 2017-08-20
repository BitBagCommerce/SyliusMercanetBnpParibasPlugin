<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Form\Type;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class MercanetBnpParibasGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'bitbag.mercanet_bnp_paribas.production' => Mercanet::PRODUCTION,
                    'bitbag.mercanet_bnp_paribas.test' => Mercanet::TEST,
                ],
                'label' => 'bitbag.mercanet_bnp_paribas.environment',
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'bitbag.mercanet_bnp_paribas.secure_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.mercanet_bnp_paribas.secure_key.not_blank',
                        'groups' => ['sylius']
                    ])
                ],
            ])
            ->add('merchant_id', TextType::class, [
                'label' => 'bitbag.mercanet_bnp_paribas.merchant_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.mercanet_bnp_paribas.merchant_id.not_blank',
                        'groups' => ['sylius']
                    ])
                ],
            ])
        ;
    }
}
