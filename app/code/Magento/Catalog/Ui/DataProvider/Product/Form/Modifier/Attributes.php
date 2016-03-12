<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;
use Magento\Framework\AuthorizationInterface;
use Magento\Ui\Component;
use Magento\Catalog\Model\Locator\LocatorInterface;

class Attributes extends AbstractModifier
{
    const GROUP_SORT_ORDER = 15;
    const GROUP_NAME = 'Attributes';
    const GROUP_CODE = 'attributes';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param UrlInterface $urlBuilder
     * @param Registry $registry
     * @param AuthorizationInterface $authorization
     * @param LocatorInterface $locator
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry,
        AuthorizationInterface $authorization,
        LocatorInterface $locator
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->authorization = $authorization;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @return boolean
     */
    protected function canAddAttributes()
    {
        $isWrapped = $this->registry->registry('use_wrapper');
        if (!isset($isWrapped)) {
            $isWrapped = true;
        }

        return $isWrapped && $this->authorization->isAllowed('Magento_Catalog::attributes_attributes');
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->canAddAttributes()) {
            return $meta;
        }

        $general = $this->getGeneralPanelName($meta);

        if (!isset($meta[static::GROUP_CODE])) {
            $meta[static::GROUP_CODE]['arguments']['data']['config'] = [
                'label' => __('Attributes'),
                'collapsible' => true,
                'dataScope' => self::DATA_SCOPE_PRODUCT,
                'sortOrder' => $this->getNextGroupSortOrder($meta, $general, static::GROUP_SORT_ORDER),
                'componentType' => Component\Form\Fieldset::NAME
            ];
        }

        $meta[static::GROUP_CODE]['arguments']['data']['config']['component'] =
            'Magento_Catalog/js/components/attributes-fieldset';
        $meta[static::GROUP_CODE]['arguments']['data']['config']['visible'] =
            !empty($meta[static::GROUP_CODE]['children']);
        $meta['add_attribute_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'options' => [
                'title' => __('Add Attribute'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'class' => 'action-secondary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Add Selected'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }.product_attributes_grid',
                                'actionName' => 'save'
                            ],
                            [
                                'closeModal'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $meta['add_attribute_modal']['children'] = [
            'product_attributes_grid' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'component' => 'Magento_Catalog/js/components/attributes-insert-listing',
                            'componentType' => Component\Container::NAME,
                            'autoRender' => false,
                            'dataScope' => 'product_attributes_grid',
                            'externalProvider' => 'product_attributes_grid.product_attributes_grid_data_source',
                            'selectionsProvider' => '${ $.ns }.${ $.ns }.product_attributes_columns.ids',
                            'ns' => 'product_attributes_grid',
                            'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                            'immediateUpdateBySelection' => true,
                            'behaviourType' => 'edit',
                            'externalFilterMode' => true,
                            'dataLinks' => ['imports' => false, 'exports' => true],

                            'formProvider' => 'ns = ${ $.namespace }, index = product_form',
                            'groupCode' => static::GROUP_CODE,
                            'groupName' => static::GROUP_NAME,
                            'groupSortOrder' => static::GROUP_SORT_ORDER,
                            'addAttributeUrl' =>
                                $this->urlBuilder->getUrl('catalog/product/addAttributeToTemplate'),
                            'productId' => $this->locator->getProduct()->getId(),
                            'productType' => $this->locator->getProduct()->getTypeId(),
                            'loading' => false,
                            'imports' => [
                                'attributeSetId' => '${ $.provider }:data.product.attribute_set_id'
                            ],
                            'exports' => [
                                'attributeSetId' => '${ $.externalProvider }:params.template_id'
                            ]
                        ],
                    ],
                ],
            ],
        ];

        return $meta;
    }
}
