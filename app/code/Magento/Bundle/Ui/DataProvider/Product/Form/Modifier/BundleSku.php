<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Bundle\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Customize SKU field
 */
class BundleSku extends AbstractModifier
{
    const CODE_SKU_TYPE = 'sku_type';
    const SORT_ORDER = 31;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($groupCode = $this->getGroupCodeByField($meta, ProductAttributeInterface::CODE_SKU)) {
            $skuPath = $this->getElementArrayPath($meta, ProductAttributeInterface::CODE_SKU);
            $meta[$groupCode]['children'][self::CODE_SKU_TYPE] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'sortOrder' => $this->getNextAttributeSortOrder(
                                $meta,
                                [ProductAttributeInterface::CODE_SKU],
                                self::SORT_ORDER
                            ),
                            'formElement' => Form\Element\Checkbox::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'label' => __('Dynamic SKU'),
                            'prefer' => 'toggle',
                            'additionalClasses' => 'admin__field-x-small',
                            'templates' => ['checkbox' => 'ui/form/components/single/switcher'],
                            'valueMap' => [
                                'false' => '1',
                                'true' => '0',
                            ],
                            'dataScope' => self::CODE_SKU_TYPE,
                            'value' => '0',
                            'scopeLabel' => $this->arrayManager->get($skuPath . '/scopeLabel', $meta),
                        ],
                    ],
                ],
            ];
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
