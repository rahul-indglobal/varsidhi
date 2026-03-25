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

namespace Webkul\StorePickup\Setup\Patch\Data;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateCMSPage implements DataPatchInterface
{
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;

    /**
     * Constructor
     * @param CategoryFactory       $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory          $blockFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $blockId = $this->createCMSBlockForPickupStores();
        if ($blockId) {
            $this->createProductCategory($blockId);
        }

        $this->createCMSBlockForPickupStoresDetails();
    }

    /**
     * create CMS block
     * @param void
     * @return int
     */
    private function createCMSBlockForPickupStores()
    {
        $block = $this->blockFactory->create();
        $block->setIdentifier('pickup-stores');
        $block->setTitle('Pickup Stores');
        $block->setIsActive(1);
        $block->setStores(0);
        $block->setContent('<p>{{widget type="Webkul\StorePickup\Block\Widget\PickupStores"}}</p>');
        $model = $block->save();
        return $model->getId();
    }

    /**
     * create CMS block details
     * @param void
     * @return int
     */
    private function createCMSBlockForPickupStoresDetails()
    {
        $block = $this->blockFactory->create();
        $block->setIdentifier('pickup-stores-details');
        $block->setTitle('Pickup Stores Details');
        $block->setIsActive(1);
        $block->setStores(0);
        $block->setContent('<p>{{widget type="Webkul\StorePickup\Block\Widget\PickupStoresDetails"}}</p>');
        $block->save();
    }

    /**
     * create Product Category
     * @param int $blockId
     * @return int
     */
    private function createProductCategory($blockId)
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $rootId = $store->getRootCategoryId();
        $rootCategory = $this->categoryFactory->create()->load($rootId);

        $category = $this->categoryFactory->create();
        $category->setName('Pickup Stores');
        $category->setIsActive(true);
        $category->setUrlKey('pickup-stores');
        $category->setData('landing_page', $blockId);
        $category->setData('display_mode', \Magento\Catalog\Model\Category::DM_PAGE);
        $category->setData('page_layout', '1column');

        $category->setParentId($rootCategory->getId());
        $category->setStoreId($storeId);
        $category->setPath($rootCategory->getPath());
        $category->save();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
