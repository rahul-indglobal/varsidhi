<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */


namespace Lof\Rma\Block\Rma;

use \Magento\Framework\View\Element\Template;

class NewRma extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lof\Rma\Helper\Data         $rmaHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->groupRepository = $groupRepository;
        $this->rmaHelper             = $rmaHelper;
         $this->imageHelper            = $imageHelper;
        $this->objectManager         = $objectManager;
        $this->request =  $context->getRequest();
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Create RMA'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            //$pageMainTitle->setPageTitle(__(' New Return for Order #'));
            $pageMainTitle->setPageTitle(__(' New Return for Order #').$this->getOrder()->getIncrementId());
        }
    }

    public function getOrder() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($this->getOrderId());
        return $order;
    }
      public function getOrderId() {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }
     public function getFormattedAddress()
    { 
        if($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getShippingAddress(), 'html');
        } else {
            return;
        }
    }

    public function getBillingAddress() {
        return $this->addressRenderer->format($this->getOrder()->getBillingAddress(), 'html');
    }

      public function getOrderDate() {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getOrder()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }



   
    
    
     
    public function getQtyAvailable($item)
    {
        return $this->rmaHelper->getItemQuantityAvaiable($item);
    }

    /**
     * @return \Lof\Rma\Model\Field[]
     */
    public function getCustomFields()
    {
        return $this->rmaHelper->getVisibleFields('initial', true,true);
    }


        /**
     * @param \Lof\Rma\Model\Field $field
     *
     * @return string
     */
    public function getFieldInputHtml(\Lof\Rma\Model\Field $field)
    {
        $params = $this->rmaHelper->getInputParams($field, false);
        unset($params['label']);
        $className = '\Magento\Framework\Data\Form\Element\\'.ucfirst(strtolower($field->getType()));
        $element = $this->objectManager->create($className);
        $element->setData($params);
        $element->setForm(new \Magento\Framework\DataObject());
        $element->setId($field->getCode());
        $element->setNoSpan(true);
        $element->addClass($field->getType());
        $element->setType($field->getType());
        if ($field->IsCustomerRequired()) {
            $element->addClass('required-entry');
        }

        return $element->getDefaultHtml();
    }


    /**
     * Initialize Helper to work with Image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Helper\Image
     */
    public function initImage($item, $imageId, $attributes = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku',$item->getData('sku'));
        return $this->imageHelper->init($product, $imageId, $attributes);
    }

    public function getAttribute($item){
        $product = $item->getProduct();
        $attribute = $product->getResource()->getAttribute('product_rma');
        $attribute_value = $attribute ->getFrontend()->getValue($product)->getText(); 
        return $attribute_value;
    }
    

    
}
