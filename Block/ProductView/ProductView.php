<?php

namespace MyModule\Module\Block\ProductView;

use Magento\Framework\View\Element\Template;
class ProductView extends Template
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $optionCollection,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->optionCollection = $optionCollection;
        parent::__construct($context,$data);
        $this->_registry = $registry;
        $this->formKey = $formKey;
    }

    public function getProductCustomOption($sku)
    {
        try {
            try {
                $product = $this->productRepository->get($sku);
            } catch (\Exception $exception) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Such product doesn\'t exist'));
            }
            $productOption = $this->optionCollection->create()->getProductOptions($product->getEntityId(),$product->getStoreId(),false);
            $optionData = [];
            foreach($productOption as $option) {
                $optionId = $option->getId();
                $optionValues = $product->getOptionById($optionId);
                if ($optionValues === null) {
                    throw \Magento\Framework\Exception\NoSuchEntityException::singleField('optionId', $optionId);
                }
                foreach($optionValues->getValues() as $values) {
                    $optionData[] = $values->getData();
                }
            }
            return $optionData;
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Such product doesn\'t exist'));
        }
        return $product;
    }
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}