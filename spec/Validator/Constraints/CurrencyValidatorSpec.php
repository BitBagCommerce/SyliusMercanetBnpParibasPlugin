<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\MercanetBnpParibasPlugin\Validator\Constraints;

use BitBag\MercanetBnpParibasPlugin\Validator\Constraints\Currency;
use BitBag\MercanetBnpParibasPlugin\Validator\Constraints\CurrencyValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Sylius\Component\Currency\Model\Currency as CurrencyModel;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class CurrencyValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyValidator::class);
    }

    function it_doesnt_allow_different_currency_than_eur(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ArrayCollection $arrayCollection,
        ChannelInterface $channel,
        CurrencyModel $currencyModel
    )
    {
        $constraint = new Currency();
        $constraint->factoryName = CurrencyValidator::FACTORY_NAME_MERCANET_BNP_PARIBAS;

        $currencyModel->getCode()->willReturn('USD');

        $channel->getBaseCurrency()->willReturn($currencyModel);

        $arrayCollection->toArray()->willReturn([$channel]);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $executionContext->buildViolation($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::any(), Argument::any())->willReturn($constraintViolationBuilder);

        $this->initialize($executionContext);
        $this->validate($arrayCollection, $constraint);
    }
}
