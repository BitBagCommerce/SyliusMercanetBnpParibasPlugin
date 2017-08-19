<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
interface OpenMercanetBnpParibasWrapperInterface
{
    /**
     * @param string $secretKey
     *
     * @return Mercanet
     */
    public function createMercanet($secretKey);

    /**
     * @param string $secretKey
     *
     * @return bool
     */
    public function paymentVerification($secretKey);

    /**
     * @return bool
     */
    public function isMethodPost();
}