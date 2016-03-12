<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BraintreeTwo\Model\Paypal\Helper;

use Magento\Quote\Model\Quote;

/**
 * Abstract class AbstractHelper
 */
abstract class AbstractHelper
{
    /**
     * Make sure addresses will be saved without validation errors
     *
     * @param Quote $quote
     * @return void
     */
    protected function disabledQuoteAddressValidation(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setShouldIgnoreValidation(true);

        if (!$quote->getIsVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShouldIgnoreValidation(true);
            if (!$billingAddress->getEmail()) {
                $billingAddress->setSameAsBilling(1);
            }
        }
    }
}
