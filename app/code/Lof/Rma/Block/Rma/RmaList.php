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

class RmaList extends \Magento\Framework\View\Element\Template
{

    public function __construct(
                \Magento\Customer\Model\Session $customerSession,
                \Lof\Rma\Api\Repository\RmaRepositoryInterface     $rmaRepository,
                 \Lof\Rma\Model\Status $statusFactory ,
                 \Magento\Framework\Api\SearchCriteriaBuilder       $searchCriteriaBuilder,
                 \Magento\Framework\Api\SortOrderBuilder            $sortOrderBuilder,
                 \Magento\Framework\View\Element\Template\Context $context,
                 array $data = []
            )
    {       
        $this->rmaRepository = $rmaRepository;
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->status         = $statusFactory;
        parent::__construct($context);
    } 
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Returns'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('My Returns'));
        }

    }

    public function GetRmaList(){
        $customer_id = $this->customerSession->getCustomerId();
         
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('main_table.customer_id',  $customer_id)
            ->addSortOrder($this->sortOrderBuilder
            ->setField('rma_id')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_DESC)
            ->create());
        $rma = $this->rmaRepository->getList($searchCriteria->create())->getItems();
         return $rma;        
    }

    /**
     * @return string
     */
    public function getNewRmaUrl()
    {
        return $this->_urlBuilder->getUrl('rma/rma/sellect');
    }
    /**
     * @return string
     */
    public function GetStatusname($id){
         $status =  $this->status->load($id);
         return $status->getName();
    }
}
