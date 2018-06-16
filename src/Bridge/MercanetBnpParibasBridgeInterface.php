<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\MercanetBnpParibasPlugin\Bridge;

use BitBag\MercanetBnpParibasPlugin\Legacy\Mercanet;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
interface MercanetBnpParibasBridgeInterface
{
    /**
     * @param string $secretKey
     *
     * @return Mercanet
     */
    public function createMercanet($secretKey);

    /**
     * @return bool
     */
    public function paymentVerification();

    /**
     * @return bool
     */
    public function isPostMethod();

    /**
     * @return string
     */
    public function getSecretKey();

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey);

    /**
     * @return string
     */
    public function getMerchantId();

    /**
     * @param string $merchantId
     */
    public function setMerchantId($merchantId);

    /**
     * @return string
     */
    public function getKeyVersion();

    /**
     * @param string $keyVersion
     */
    public function setKeyVersion($keyVersion);

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @param string $environment
     */
    public function setEnvironment($environment);
}
