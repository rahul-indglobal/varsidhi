<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\StorePickup\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\CategoryFactory;

class AfterConfigSave implements ObserverInterface
{
    /**
     * @var $request
     */
    protected $request;

    /**
     * @var $dataHelper
     */
    protected $dataHelper;

    /**
     * @var Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        CategoryFactory $categoryFactory
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * execute
     * @param Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('url_key', ['eq' => 'pickup-stores']);

        $isPickupStoresPageEnabled = $this->dataHelper->isPickupStoresPageEnabled();
        $isModuleEnabled = $this->dataHelper->isModuleEnabled();

        $active = false;
        if ($isPickupStoresPageEnabled && $isModuleEnabled) {
            $active = true;
        }

        foreach ($collection as $category) {
            $category->setIsActive($active);
            $category->setIncludeInMenu($active);
            $this->saveCategory($category);
            $category->setStoreId(0);
            $this->saveCategory($category);
        }
    }

    /**
     * save category
     * @param object $category
     * @return void
     */
    private function saveCategory($category)
    {
        $category->save();
    }
}
