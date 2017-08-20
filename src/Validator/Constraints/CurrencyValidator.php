<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
class CurrencyValidator extends ConstraintValidator
{
    const FACTORY_NAME_MERCANET_BNP_PARIBAS = 'mercanet_bnp_paribas';

    const CURRENCY_CODE_EUR = 'EUR';

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        Assert::notEmpty($constraint->factoryName);

        Assert::isInstanceOf($value, ArrayCollection::class);

        if ($constraint->factoryName !== self::FACTORY_NAME_MERCANET_BNP_PARIBAS) {

            return;
        }

        /** @var ChannelInterface $channel */
        foreach ($value->toArray() as $channel) {

            Assert::isInstanceOf($channel, ChannelInterface::class);

            if ($channel->getBaseCurrency()->getCode() !== self::CURRENCY_CODE_EUR) {

                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation()
                ;
            }
        }
    }
}