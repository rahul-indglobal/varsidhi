<?php

namespace Varsidhi\Homepage\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Zend_Db_Expr;

class CategoryListing extends AbstractProduct
{
	/**
	 * @var CollectionFactory
	 */
	protected $productCollectionFactory;

	/**
	 * @var Visibility
	 */
	protected $catalogProductVisibility;

	/**
	 * @var DateTime
	 */
	protected $dateTime;

	/**
	 * @var UrlHelper
	 */
	protected $urlHelper;

	/**
	 * @param Context $context
	 * @param CollectionFactory $productCollectionFactory
	 * @param Visibility $catalogProductVisibility
	 * @param DateTime $dateTime
	 * @param UrlHelper $urlHelper
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		CollectionFactory $productCollectionFactory,
		Visibility $catalogProductVisibility,
		DateTime $dateTime,
		UrlHelper $urlHelper,
		array $data = []
	) {
		$this->productCollectionFactory = $productCollectionFactory;
		$this->catalogProductVisibility = $catalogProductVisibility;
		$this->dateTime = $dateTime;
		$this->urlHelper = $urlHelper;
		parent::__construct($context, $data);
	}

	/**
	 * Get store manager
	 *
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager()
	{
		return $this->_storeManager;
	}

	/**
	 * Get post parameters
	 *
	 * @param \Magento\Catalog\Model\Product $product
	 * @return array
	 */
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
	{
		$url = $this->getAddToCartUrl($product, ['_escape' => false]);
		return [
			'action' => $url,
			'data' => [
				'product' => (int) $product->getEntityId(),
				ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
			]
		];
	}

	/**
	 * Get Product Collection
	 *
	 * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
	 */
	public function getProductCollection()
	{
		$categoryId = $this->getData('category_id');
		if ($categoryId && strpos($categoryId, 'category/') !== false) {
			$categoryId = str_replace('category/', '', $categoryId);
		}
		$productsCount = (int)$this->getData('products_count') ?: 8;

		$visibleProducts = $this->catalogProductVisibility->getVisibleInCatalogIds();
		$collection = $this->productCollectionFactory->create()->setVisibility($visibleProducts);
		$collection->addAttributeToSelect('*')
			->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->addStoreFilter($this->_storeManager->getStore()->getId())
			->setPageSize($productsCount);

		if ($categoryId) {
			$collection->addCategoriesFilter(['in' => $categoryId]);
		}

		return $collection;
	}

	/**
	 * Get end of day date
	 *
	 * @return string
	 */
	public function getEndOfDayDate()
	{
		return $this->dateTime->gmtDate('Y-m-d 23:59:59');
	}

	/**
	 * Get start of day date
	 *
	 * @return string
	 */
	public function getStartOfDayDate()
	{
		return $this->dateTime->gmtDate('Y-m-d 00:00:00');
	}

	/**
	 * Get products count
	 *
	 * @return int
	 */
	public function getProductsCount()
	{
		return $this->getData('products_count') ?: 10;
	}
}
