<?php

/**
 * This file is a part of OpenSource Mercanet payment library adjusted for purposes of this project.
 */

namespace BitBag\MercanetBnpParibasPlugin\Legacy;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
class Mercanet
{
    const TEST = "https://payment-webinit-mercanet.test.sips-atos.com/rs-services/v2/paymentInit";
    const PRODUCTION = "https://payment-webinit.mercanet.bnpparibas.net/rs-services/v2/paymentInit";

    const INTERFACE_VERSION = "IR_WS_2.17";
    const INSTALMENT = "INSTALMENT";

    // BYPASS3DS
    const BYPASS3DS_ALL = "ALL";
    const BYPASS3DS_MERCHANTWALLET = "MERCHANTWALLET";

    private $brandsmap = array(
        'ACCEPTGIRO' => 'CREDIT_TRANSFER',
        'AMEX' => 'CARD',
        'BCMC' => 'CARD',
        'BUYSTER' => 'CARD',
        'BANK CARD' => 'CARD',
        'CB' => 'CARD',
        'IDEAL' => 'CREDIT_TRANSFER',
        'INCASSO' => 'DIRECT_DEBIT',
        'MAESTRO' => 'CARD',
        'MASTERCARD' => 'CARD',
        'MASTERPASS' => 'CARD',
        'MINITIX' => 'OTHER',
        'NETBANKING' => 'CREDIT_TRANSFER',
        'PAYPAL' => 'CARD',
        'PAYLIB' => 'CARD',
        'REFUND' => 'OTHER',
        'SDD' => 'DIRECT_DEBIT',
        'SOFORT' => 'CREDIT_TRANSFER',
        'VISA' => 'CARD',
        'VPAY' => 'CARD',
        'VISA ELECTRON' => 'CARD',
        'CBCONLINE' => 'CREDIT_TRANSFER',
        'KBCONLINE' => 'CREDIT_TRANSFER'
    );

    /** @var ShaComposer */
    private $secretKey;

    private $pspURL = self::TEST;

    private $responseData;

    private $parameters = array();

    private $pspFields = array(
        'amount', 'cardExpiryDate', 'cardNumber', 'cardCSCValue',
        'currencyCode', 'merchantId', 'interfaceVersion', 'sealAlgorithm',
        'transactionReference', 'keyVersion', 'paymentMeanBrand', 'customerLanguage',
        'billingAddress.city', 'billingAddress.company', 'billingAddress.country',
        'billingAddress', 'billingAddress.postBox', 'billingAddress.state',
        'billingAddress.street', 'billingAddress.streetNumber', 'billingAddress.zipCode',
        'billingContact.email', 'billingContact.firstname', 'billingContact.gender',
        'billingContact.lastname', 'billingContact.mobile', 'billingContact.phone',
        'customerAddress', 'customerAddress.city', 'customerAddress.company',
        'customerAddress.country', 'customerAddress.postBox', 'customerAddress.state',
        'customerAddress.street', 'customerAddress.streetNumber', 'customerAddress.zipCode',
        'customerEmail', 'customerContact', 'customerContact.email', 'customerContact.firstname',
        'customerContact.gender', 'customerContact.lastname', 'customerContact.mobile',
        'customerContact.phone', 'customerContact.title', 'expirationDate', 'automaticResponseUrl',
        'templateName', 'paymentMeanBrandList', 'instalmentData.number', 'instalmentData.datesList',
        'instalmentData.transactionReferencesList', 'instalmentData.amountsList', 'paymentPattern',
        'captureDay', 'captureMode', 'merchantTransactionDateTime', 'fraudData.bypass3DS', 'seal',
        'orderChannel', 'orderId', 'returnContext', 'transactionOrigin', 'merchantWalletId', 'paymentMeanId'
    );

    private $requiredFields = array(
        'amount', 'currencyCode', 'interfaceVersion', 'keyVersion', 'merchantId', 'normalReturnUrl', 'orderChannel',
        'transactionReference'
    );

    public $allowedlanguages = array(
        'nl', 'fr', 'de', 'it', 'es', 'cy', 'en'
    );

    private static $currencies = array(
        'EUR' => '978', 'USD' => '840', 'CHF' => '756', 'GBP' => '826',
        'CAD' => '124', 'JPY' => '392', 'MXP' => '484', 'TRY' => '949',
        'AUD' => '036', 'NZD' => '554', 'NOK' => '578', 'BRC' => '986',
        'ARP' => '032', 'KHR' => '116', 'TWD' => '901', 'SEK' => '752',
        'DKK' => '208', 'KRW' => '410', 'SGD' => '702', 'XPF' => '953',
        'XOF' => '952'
    );

    public static function convertCurrencyToCurrencyCode($currency)
    {
        if (!in_array($currency, array_keys(self::$currencies)))
            throw new \InvalidArgumentException("Unknown currencyCode $currency.");
        return self::$currencies[$currency];
    }

    public static function convertCurrencyCodeToCurrency($code)
    {
        if (!in_array($code, array_values(self::$currencies)))
            throw new \InvalidArgumentException("Unknown Code $code.");
        return array_search($code, self::$currencies);
    }

    public static function getCurrencies()
    {
        return self::$currencies;
    }

    public function __construct($secret)
    {
        $this->secretKey = $secret;
    }

    public function shaCompose(array $parameters)
    {
        // compose SHA string
        $shaString = '';
        foreach ($parameters as $key => $value) {
            if ($key != 'keyVersion') {
                if (is_array($value)) {
                    shaCompose($value);
                } else {
                    $shaString .= $value;
                }
            }
        }
        $shaString = str_replace("[", "", $shaString);
        $shaString = str_replace("]", "", $shaString);
        $shaString = str_replace("\",\"", "", $shaString);
        $shaString = str_replace("\"", "", $shaString);
        return $shaString;
    }

    /** @return string */
    public function getShaSign()
    {
        $this->validate();
        return hash_hmac('sha256', utf8_encode($this->shaCompose($this->toArray())), $this->secretKey);
    }

    /** @return string */
    public function getUrl()
    {
        return $this->pspURL;
    }

    public function setUrl($pspUrl)
    {
        $this->validateUri($pspUrl);
        $this->pspURL = $pspUrl;
    }

    public function setNormalReturnUrl($url)
    {
        $this->validateUri($url);
        $this->parameters['normalReturnUrl'] = $url;
    }

    public function setAutomaticResponseUrl($url)
    {
        $this->validateUri($url);
        $this->parameters['automaticResponseUrl'] = $url;
    }

    public function setTransactionReference($transactionReference)
    {
        if (preg_match('/[^a-zA-Z0-9_-]/', $transactionReference)) {
            throw new \InvalidArgumentException("TransactionReference cannot contain special characters");
        }
        $this->parameters['transactionReference'] = $transactionReference;
    }

    /**
     * Set amount in cents, eg EUR 12.34 is written as 1234
     */
    public function setAmount($amount)
    {
        if (!is_int($amount)) {
            throw new \InvalidArgumentException("Integer expected. Amount is always in cents");
        }
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be a positive number");
        }
        $this->parameters['amount'] = $amount;

    }

    public function setMerchantId($merchantId)
    {
        $this->parameters['merchantId'] = $merchantId;
    }

    public function setKeyVersion($keyVersion)
    {
        $this->parameters['keyVersion'] = $keyVersion;
    }

    public function setCurrency($currency)
    {
        if (!array_key_exists(strtoupper($currency), self::getCurrencies())) {
            throw new \InvalidArgumentException("Unknown currency");
        }
        $this->parameters['currencyCode'] = self::convertCurrencyToCurrencyCode($currency);
    }

    public function setLanguage($language)
    {
        if (!in_array($language, $this->allowedlanguages)) {
            throw new \InvalidArgumentException("Invalid language locale");
        }
        $this->parameters['customerLanguage'] = $language;
    }

    public function setCustomerEmail($email)
    {
        $this->parameters['customerEmail'] = $email;
    }

    public function setPaymentBrand($brand)
    {
        $this->parameters['paymentMeanBrandList'] = '';
        if (!array_key_exists(strtoupper($brand), $this->brandsmap)) {
            throw new \InvalidArgumentException("Unknown Brand [$brand].");
        }
        $this->parameters['paymentMeanBrandList'] = strtoupper($brand);
    }

    public function setBillingContactEmail($email)
    {
        if (strlen($email) > 50) {
            throw new \InvalidArgumentException("Email is too long");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email is invalid");
        }
        $this->parameters['billingContact.email'] = $email;
    }

    public function setBillingAddressStreet($street)
    {
        if (strlen($street) > 35) {
            throw new \InvalidArgumentException("street is too long");
        }
        $this->parameters['billingAddress.street'] = \Normalizer::normalize($street);
    }

    public function setBillingAddressStreetNumber($nr)
    {
        if (strlen($nr) > 10) {
            throw new \InvalidArgumentException("streetNumber is too long");
        }
        $this->parameters['billingAddress.streetNumber'] = \Normalizer::normalize($nr);
    }

    public function setBillingAddressZipCode($zipCode)
    {
        if (strlen($zipCode) > 10) {
            throw new \InvalidArgumentException("zipCode is too long");
        }
        $this->parameters['billingAddress.zipCode'] = \Normalizer::normalize($zipCode);
    }

    public function setBillingAddressCity($city)
    {
        if (strlen($city) > 25) {
            throw new \InvalidArgumentException("city is too long");
        }
        $this->parameters['billingAddress.city'] = \Normalizer::normalize($city);
    }

    public function setBillingContactPhone($phone)
    {
        if (strlen($phone) > 30) {
            throw new \InvalidArgumentException("phone is too long");
        }
        $this->parameters['billingContact.phone'] = $phone;
    }

    public function setBillingContactFirstname($firstname)
    {
        $this->parameters['billingContact.firstname'] = str_replace(array("'", '"'), '', \Normalizer::normalize($firstname)); // replace quotes
    }

    public function setBillingContactLastname($lastname)
    {
        $this->parameters['billingContact.lastname'] = str_replace(array("'", '"'), '', \Normalizer::normalize($lastname)); // replace quotes
    }

    public function setCaptureDay($number)
    {
        if (strlen($number) > 2) {
            throw new \InvalidArgumentException("captureDay is too long");
        }
        $this->parameters['captureDay'] = $number;
    }

    public function setCaptureMode($value)
    {
        if (strlen($value) > 20) {
            throw new \InvalidArgumentException("captureMode is too long");
        }
        $this->parameters['captureMode'] = $value;
    }

    public function setMerchantTransactionDateTime($value)
    {
        if (strlen($value) > 25) {
            throw new \InvalidArgumentException("merchantTransactionDateTime is too long");
        }
        $this->parameters['merchantTransactionDateTime'] = $value;
    }

    public function setInterfaceVersion($value)
    {
        $this->parameters['interfaceVersion'] = $value;
    }

    public function setSealAlgorithm($value)
    {
        $this->parameters['sealAlgorithm'] = $value;
    }

    public function setOrderChannel($value)
    {
        if (strlen($value) > 20) {
            throw new \InvalidArgumentException("orderChannel is too long");
        }
        $this->parameters['orderChannel'] = $value;
    }

    public function setOrderId($value)
    {
        if (strlen($value) > 32) {
            throw new \InvalidArgumentException("orderId is too long");
        }
        $this->parameters['orderId'] = $value;
    }

    public function setReturnContext($value)
    {
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException("returnContext is too long");
        }
        $this->parameters['returnContext'] = $value;
    }

    public function setTransactionOrigin($value)
    {
        if (strlen($value) > 20) {
            throw new \InvalidArgumentException("transactionOrigin is too long");
        }
        $this->parameters['transactionOrigin'] = $value;
    }

    // Methodes liees a la carte
    public function setCardNumber($number)
    {
        if (strlen($number) > 19) {
            throw new \InvalidArgumentException("cardNumber is too long");
        }
        if (strlen($number) < 12) {
            throw new \InvalidArgumentException("cardNumber is too short");
        }
        $this->parameters['cardNumber'] = $number;
    }

    public function setCardExpiryDate($date)
    {
        if (strlen($date) != 6) {
            throw new \InvalidArgumentException("cardExpiryDate value is invalid");
        }
        $this->parameters['cardExpiryDate'] = $date;
    }

    public function setCardCSCValue($value)
    {
        if (strlen($value) > 4) {
            throw new \InvalidArgumentException("cardCSCValue value is invalid");
        }
        $this->parameters['cardCSCValue'] = $value;
    }

    // Methodes liees a la lutte contre la fraude

    public function setFraudDataBypass3DS($value)
    {
        if (strlen($value) > 128) {
            throw new \InvalidArgumentException("fraudData.bypass3DS is too long");
        }
        $this->parameters['fraudData.bypass3DS'] = $value;
    }

    // Methodes liees au paiement one-click

    public function setMerchantWalletId($wallet)
    {
        if (strlen($wallet) > 21) {
            throw new \InvalidArgumentException("merchantWalletId is too long");
        }
        $this->parameters['merchantWalletId'] = $wallet;
    }

    public function setPaymentMeanId($value)
    {
        if (strlen($value) > 6) {
            throw new \InvalidArgumentException("paymentMeanId is too long");
        }
        $this->parameters['paymentMeanId'] = $value;
    }

    // Methodes liees au paiement en n-fois

    public function setInstalmentDataNumber($number)
    {
        if (strlen($number) > 2) {
            throw new \InvalidArgumentException("instalmentData.number is too long");
        }
        if (($number < 2) || ($number > 50)) {
            throw new \InvalidArgumentException("instalmentData.number invalid value : value must be set between 2 and 50");
        }
        $this->parameters['instalmentData.number'] = $number;
    }

    public function setInstalmentDatesList($datesList)
    {
        $this->parameters['instalmentData.datesList'] = $datesList;
    }

    public function setInstalmentDataTransactionReferencesList($transactionReferencesList)
    {
        $this->parameters['instalmentData.transactionReferencesList'] = $transactionReferencesList;
    }

    public function setInstalmentDataAmountsList($amountsList)
    {
        $this->parameters['instalmentData.amountsList'] = $amountsList;
    }

    public function setPaymentPattern($paymentPattern)
    {
        $this->parameters['paymentPattern'] = $paymentPattern;
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'set') {
            $field = lcfirst(substr($method, 3));
            if (in_array($field, $this->pspFields)) {
                $this->parameters[$field] = $args[0];
                return;
            }
        }

        if (substr($method, 0, 3) == 'get') {
            $field = lcfirst(substr($method, 3));
            if (array_key_exists($field, $this->parameters)) {
                return $this->parameters[$field];
            }
        }

        throw new \BadMethodCallException("Unknown method $method");
    }

    public function toArray()
    {
        ksort($this->parameters);
        return $this->parameters;
    }

    public function toParameterString()
    {
        ksort($this->parameters);

        $dataName = "";
        $parameterArray = array();
        $chaine = '{';
        foreach ($this->parameters as $key => $val) {
            $dataArray = explode(".", $key);
            if (count($dataArray) > 1) {
                if ($dataName == $dataArray[0]) {
                    $parameterArray[$dataArray[1]] = $val;
                } else {
                    if ($dataName != "") {
                        if (strlen($chaine) != 1) {
                            $chaine .= ",";
                        }
                        $chaine .= '"' . $dataName . '":' . json_encode($parameterArray);
                    }
                    unset($parameterArray);
                    $parameterArray = array();
                    $dataName = $dataArray[0];
                    $parameterArray[$dataArray[1]] = $val;
                }
            } else {
                if ($dataName != "") {
                    if (strlen($chaine) != 1) {
                        $chaine .= ",";
                    }
                    $chaine .= '"' . $dataName . '":' . json_encode($parameterArray);
                    $dataName = "";
                }
                if (strlen($chaine) != 1) {
                    $chaine .= ",";
                }
                $chaine .= '"' . $key . '":"' . $val . '"';
            }
        }
        if ($dataName != "") {
            if (strlen($chaine) != 1) {
                $chaine .= ",";
            }
            $chaine .= '"' . $dataName . '":' . json_encode($parameterArray);
        }

        $chaine .= ',"seal" : "' . $this->getShaSign() . '" }';
        $chaine = str_replace(":\"[", ":[", $chaine);
        $chaine = str_replace("]\"", "]", $chaine);
        $chaine = str_replace("\\\"", "\"", $chaine);
        return $chaine;
    }
    /*
    if (substr($val, 0, 1) == "[")
                      $chaine .= '"'.$key.'":'.$val;
                    else
                      $chaine .= '"'.$key.'":"'.$val.'"'; */

    /** @return PaymentRequest */
    public static function createFromArray(ShaComposer $shaComposer, array $parameters)
    {
        $instance = new static($shaComposer);
        foreach ($parameters as $key => $value) {
            $instance->{"set$key"}($value);
        }
        return $instance;
    }

    public function validate()
    {
        foreach ($this->requiredFields as $field) {
            if (empty($this->parameters[$field])) {
                throw new \RuntimeException($field . " can not be empty");
            }
        }
    }

    protected function validateUri($uri)
    {
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Uri is not valid");
        }
        if (strlen($uri) > 200) {
            throw new \InvalidArgumentException("Uri is too long");
        }
    }

    // Traitement des reponses de Mercanet
    // -----------------------------------

    /** @var string */
    const SHASIGN_FIELD = "SEAL";

    /** @var string */
    const DATA_FIELD = "DATA";

    public function setResponse(array $httpRequest)
    {
        // use lowercase internally
        $httpRequest = array_change_key_case($httpRequest, CASE_UPPER);

        // set sha sign        
        $this->shaSign = $this->extractShaSign($httpRequest);

        // filter request for Sips parameters
        $this->parameters = $this->filterRequestParameters($httpRequest);
    }

    /**
     * @var string
     */
    private $shaSign;

    private $dataString;

    private $responseRequest;

    private $parameterArray;

    /**
     * Filter http request parameters
     * @param array $httpRequest
     * @return array
     */
    private function filterRequestParameters(array $httpRequest)
    {
        //filter request for Sips parameters
        if (!array_key_exists(self::DATA_FIELD, $httpRequest) || $httpRequest[self::DATA_FIELD] == '') {
            throw new \InvalidArgumentException('Data parameter not present in parameters.');
        }
        $parameters = array();
        $this->responseData = $httpRequest[self::DATA_FIELD];
        $dataString = $httpRequest[self::DATA_FIELD];
        $this->dataString = $dataString;
        $dataParams = explode('|', $dataString);
        foreach ($dataParams as $dataParamString) {
            $dataKeyValue = explode('=', $dataParamString, 2);
            $parameters[$dataKeyValue[0]] = $dataKeyValue[1];
        }

        return $parameters;
    }

    public function getSeal()
    {
        return $this->shaSign;
    }

    private function extractShaSign(array $parameters)
    {
        if (!array_key_exists(self::SHASIGN_FIELD, $parameters) || $parameters[self::SHASIGN_FIELD] == '') {
            throw new \InvalidArgumentException('SHASIGN parameter not present in parameters.');
        }

        return $parameters[self::SHASIGN_FIELD];
    }

    /**
     * Checks if the response is valid
     * @return bool
     */
    public function isValid()
    {
        $resultat = false;

        $signature = $this->responseData;
        $compute = hash('sha256', utf8_encode($signature . $this->secretKey));
        if (strcmp($this->shaSign, $compute) == 0) {
            if ((strcmp($this->parameters['responseCode'], "00") == 0) || (strcmp($this->parameters['responseCode'], "60") == 0)) {
                $resultat = true;
            }
        }
        return $resultat;
    }

    function getXmlValueByTag($inXmlset, $needle)
    {
        $resource = xml_parser_create();//Create an XML parser
        xml_parse_into_struct($resource, $inXmlset, $outArray);// Parse XML data into an array structure
        xml_parser_free($resource);//Free an XML parser
        for ($i = 0; $i < count($outArray); $i++) {
            if ($outArray[$i]['tag'] == strtoupper($needle)) {
                $tagValue = $outArray[$i]['value'];
            }
        }
        return $tagValue;
    }

    /**
     * Retrieves a response parameter
     * @param string $key
     * @throws \InvalidArgumentException
     */
    public function getParam($key)
    {
        return $this->parameterArray[$key];
    }

    public function getResponseRequest()
    {
        return $this->responseRequest;
    }

    public function executeRequest()
    {
        //echo "URL = " . $this->getUrl() . "<br>";
        //echo "param = " . $this->toParameterString() . "<br>";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->toParameterString());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:application/json'));
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch); // $this->responseRequest et 	$this->responseStatus = false;
        $info = curl_getinfo($ch);
//        print_r($result);

        if (!$result) {
            Print "curl error: " . curl_error($ch) . "\n";
            curl_close($ch);
            die();
        }

        if ($info['http_code'] != 200) {
            Print "service error: " . $info['http_code'] . "\n";
            Print "return: " . $result . "\n";
            curl_close($ch);
            die();
        }
        curl_close($ch);

        if (strlen($result) == 0) {
            Print "service did not sent back data\n";
            die();
        }
        $result_array = json_decode($result);
        //print_r($result_array);

        if ($result_array->redirectionStatusCode == "00") {

            return "<html><body><form name=\"redirectForm\" method=\"POST\" action=\"" . $result_array->redirectionUrl . "\">" .
                "<input type=\"hidden\" name=\"redirectionVersion\" value=\"" . $result_array->redirectionVersion . "\">" .
                "<input type=\"hidden\" name=\"redirectionData\" value=\"" . $result_array->redirectionData . "\">" .
                "<noscript><input type=\"submit\" name=\"Go\" value=\"Click to continue\"/></noscript> </form>" .
                "<script type=\"text/javascript\"> document.redirectForm.submit(); </script>" .
                "</body></html>";
        }
    }
}