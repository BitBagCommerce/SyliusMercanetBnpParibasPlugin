<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class Currency extends Constraint
{
    /**
     * @var string
     */
    public $message = "Invalid currency. Allowed currency is EUR.";

    /**
     * @var null|string
     */
    public $factoryName;

    /**
     * @return string
     */
    public function validatedBy()
    {
        return CurrencyValidator::class;
    }

}