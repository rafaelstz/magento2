<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BraintreeTwo\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\BraintreeTwo\Gateway\Config\Config;
use Magento\BraintreeTwo\Gateway\Config\PayPal\Config as PayPalConfig;
use Magento\BraintreeTwo\Model\Adapter\BraintreeAdapter;
use Magento\Framework\Locale\ResolverInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'braintreetwo';

    const PAYPAL_CODE = 'braintreetwo_paypal';

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PayPalConfig
     */
    private $payPalConfig;

    /**
     * @var BraintreeAdapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $clientToken = '';

    /**
     * Constructor
     *
     * @param Config $config
     * @param PayPalConfig $payPalConfig;
     * @param BraintreeAdapter $adapter
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        Config $config,
        PayPalConfig $payPalConfig,
        BraintreeAdapter $adapter,
        ResolverInterface $localeResolver
    ) {
        $this->config = $config;
        $this->payPalConfig = $payPalConfig;
        $this->adapter = $adapter;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $isPayPalActive = $this->payPalConfig->isActive();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'isSingleUse' => !$isPayPalActive,
                    'clientToken' => $this->getClientToken(),
                    'ccTypesMapper' => $this->config->getCctypesMapper(),
                    'sdkUrl' => $this->config->getSdkUrl(),
                    'countrySpecificCardTypes' => $this->config->getCountrySpecificCardTypeConfig(),
                    'availableCardTypes' => $this->config->getAvailableCardTypes(),
                    'useCvv' => $this->config->isCvvEnabled(),
                    'environment' => $this->config->getEnvironment(),
                    'kountMerchantId' => $this->config->getKountMerchantId(),
                    'hasFraudProtection' => $this->config->hasFraudProtection(),
                    'merchantId' => $this->config->getMerchantId(),
                ],
                Config::CODE_3DSECURE => [
                    'enabled' => $this->config->isVerify3DSecure(),
                    'thresholdAmount' => $this->config->getThresholdAmount(),
                    'specificCountries' => $this->config->get3DSecureSpecificCountries()
                ],
                self::PAYPAL_CODE => [
                    'isActive' => $isPayPalActive,
                    'title' => $this->payPalConfig->getTitle(),
                    'isAllowShippingAddressOverride' => $this->payPalConfig->isAllowToEditShippingAddress(),
                    'merchantName' => $this->payPalConfig->getMerchantName(),
                    'locale' => strtolower($this->localeResolver->getLocale()),
                    'paymentAcceptanceMarkSrc' =>
                        'https://www.paypalobjects.com/webstatic/en_US/i/buttons/pp-acceptance-medium.png',
                ]
            ]
        ];
    }

    /**
     * Generate a new client token if necessary
     * @return string
     */
    public function getClientToken()
    {
        if (empty($this->clientToken)) {
            $this->clientToken = $this->adapter->generate();
        }

        return $this->clientToken;
    }
}
