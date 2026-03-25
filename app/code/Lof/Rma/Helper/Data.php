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



namespace Lof\Rma\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder          $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder               $sortOrderBuilder,
        \Lof\Rma\Api\Repository\ConditionRepositoryInterface  $conditionRepository,
        \Lof\Rma\Api\Repository\ReasonRepositoryInterface     $reasonRepository,
        \Lof\Rma\Api\Repository\ResolutionRepositoryInterface $resolutionRepository,
        \Lof\Rma\Api\Repository\MessageRepositoryInterface    $messageRepository,
        \Lof\Rma\Api\Repository\ItemRepositoryInterface       $itemRepository,
        \Lof\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository,
        \Lof\Rma\Api\Repository\FieldRepositoryInterface      $fieldRepository,
        \Lof\Rma\Model\ResourceModel\OrderStatusHistory\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory                           $productFactory,
        \Lof\Rma\Helper\Help                                              $Helper,
        \Magento\User\Model\UserFactory $userFactory,
        \Lof\Rma\Api\Repository\StatusRepositoryInterface             $statusRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Message\ManagerInterface          $messageManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Helper\Context                 $context
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->conditionRepository   = $conditionRepository;
        $this->reasonRepository      = $reasonRepository;
        $this->resolutionRepository  = $resolutionRepository;
        $this->messageRepository     = $messageRepository;
        $this->itemRepository        = $itemRepository;
        $this->attachmentRepository  = $attachmentRepository;
        $this->fieldRepository       = $fieldRepository;
        $this->customerFactory       = $customerFactory;
        $this->userFactory           = $userFactory;
        $this->productFactory        = $productFactory;
        $this->statusRepository      = $statusRepository;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->helper                   = $Helper; 
        $this->localeDate              = $localeDate;
         $this->_resource      = $resource;
        $this->messageManager          = $messageManager;
        $this->context = $context;
        parent::__construct($context);
    }
      
    public function getCreditMemoIds($rmaId = 0){
        if($rmaId){
            $connection = $this->_resource->getConnection();
                $select = 'SELECT rc_credit_memo_id  FROM ' . $this->_resource->getTableName('lof_rma_rma_creditmemo') . ' WHERE rc_rma_id = ' .(int)$rmaId . ' ORDER BY rc_credit_memo_id ASC';
            $this->rc_credit_memo_id  = $connection->fetchAll($select);
            $result = array_column($this->rc_credit_memo_id, 'rc_credit_memo_id'); 
            return $result ;
        }
        return [];
    }
    public function getExchangeOrderIds($rmaId = 0){
        if($rmaId) {
            $connection = $this->_resource->getConnection();
            $select = 'SELECT re_exchange_order_id  FROM ' . $this->_resource->getTableName('lof_rma_rma_order') . ' WHERE re_rma_id = ' .(int)$rmaId . ' ORDER BY re_exchange_order_id ASC';
            $this->_exorderid = $connection->fetchAll($select);
            
            $result = array_column($this->_exorderid, 're_exchange_order_id'); 
            return $result ;
        }
        return [];
    }

    public function getProductById($id)
    {
        $product = $this->productFactory->create()->load($id);
        return $product;
    }
    /**
     * @return \Lof\Rma\Api\Data\ConditionInterface[]
     */
    public function getConditions()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->conditionRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lof\Rma\Api\Data\ReasonInterface[]
     */
    public function getReasons()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->reasonRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lof\Rma\Api\Data\ResolutionInterface[]
     */
    public function getResolutions()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->resolutionRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lof\Rma\Api\Data\StatusInterface[]
     */
    public function getStatusList()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->statusRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return array
     */
    public function getConditionOptionArray()
    {
        $array = [];
        $conditions = $this->getConditions();
        /** @var \Lof\Rma\Api\Data\ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $array[$condition->getId()] = $condition->getName();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getReasonOptionArray()
    {
        $array = [];
        $reasons = $this->getReasons();
        /** @var \Lof\Rma\Api\Data\ReasonInterface $reason */
        foreach ($reasons as $reason) {
            $array[$reason->getId()] = $reason->getName();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getResolutionOptionArray()
    {
        $array = [];
        $resolutions = $this->getResolutions();
        /** @var \Lof\Rma\Api\Data\ResolutionInterface $resolution */
        foreach ($resolutions as $resolution) {
            $array[$resolution->getId()] = $resolution->getName();
        }

        return $array;
    }

        /**
     * {@inheritdoc}
     */
    public function getMessages(\Lof\Rma\Api\Data\RmaInterface $rma,$isfrontend=false)
    {
        $order = $this->sortOrderBuilder
            ->setField('message_id')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId());
        if($isfrontend)
           $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_visible_in_frontend', '1');
        $searchCriteria->addSortOrder($order);

        return $this->messageRepository->getList($searchCriteria->create())->getItems();
    }
     /**
     * {@inheritdoc}
     */
    public function getItems(\Lof\Rma\Api\Data\RmaInterface $rma)
    {
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filterBuilder = $objectManager->get(\Magento\Framework\Api\FilterBuilder::class);
       $filterQtyRequest = $filterBuilder->setField()
            ->setValue('0')
            ->setConditionType('neq')
            ->create();*/
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId())
            ->addFilter('qty_requested',0,'neq')
        ;
        //return $this->itemRepository->getList($searchCriteria->create())->getItems();
        $sc = $searchCriteria->create();
        $rp = $this->itemRepository;
        $ls = $rp->getList($sc);
        $arr = $ls->getItems();
        return $arr;


    }

    public function getItemQuantityAvaiable($orderItem){
        $qtyShipped = $orderItem->getData('qty_shipped');
    
        if ($this->helper->getConfig($store = null,'rma/policy/return_only_shipped')) {
            $qty = $qtyShipped - $this->getQtyReturned($orderItem);
        }else{         
            $qty = $orderItem->getData('qty_ordered') - $this->getQtyReturned($orderItem);
        }
        
        if ($qty < 0) {
            $qty = 0;
        }
        return (int)$qty;
    }

      /**
     * {@inheritdoc}
     */
    public function getQtyReturned($orderItem)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_item_id', $orderItem->getData('item_id'))
        ;

        $items = $this->itemRepository->getList($searchCriteria->create())->getItems();
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item->getQtyRequested();
        }

        return $sum;
    }
       /**
     * {@inheritdoc}
     */
    public function getRmaItems($orderItem,$rmaid){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_item_id', $orderItem->getData('item_id'))
            ->addFilter('rma_id', $rmaid)
        ;

        $items = $this->itemRepository->getList($searchCriteria->create())->getItems();
        return $items;
    }

    
    
    public function getQtyReturnedRma($orderItem,$rmaid)
    {
        $items =  $this->getRmaItems($orderItem,$rmaid);
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item->getQtyRequested();
        }

        return $sum;
    }
    public function getRmaItemData($orderItem,$rmaid){
        $items =  $this->getRmaItems($orderItem,$rmaid);
        $itemData = array();
        foreach ($items as $item) {
            $itemData = $item->getData();
        }
        return $itemData;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMessage(\Lof\Rma\Api\Data\RmaInterface $rma)
    {
        $messages = $this->getMessages($rma);

        return array_pop($messages);
    }
    /**
     * {@inheritdoc}
     */
    public function getAttachments($itemType, $itemId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('item_id', $itemId)
            ->addFilter('item_type', $itemType)
            ->create();

        return $this->attachmentRepository->getList($searchCriteria)->getItems();
    }
        /**
     * {@inheritdoc}
     */
    public function getCustomerEmail($id)
    {
        return $this->customerFactory->create()->load($id)->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName($user_id)
    {
        
            return $this->userFactory->create()->load($user_id)->getName();
    }
  
    /**
     * @param string $Option
     * @return array
     */
    public function getAdminOptionArray($Option = false)
    {
        $arr = $this->userFactory->create()->getCollection()->toArray();
        $result = [];
         foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'] . ' ' . $value['lastname'];
        }
        if ($Option) {
            $result[0] = __('-- Please Select --');
        }
        return $result;
    }
        /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create(); 
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addSortOrder($sortOrder)
        ;

        return $this->fieldRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleFields($status,$isEdit)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)            
            ->addSortOrder($this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create())
        ;
             $searchCriteria->addFilter('is_editable_customer', true)
             ->addFilter('visible_customer_status', "%,$status,%", 'like');


        return $this->fieldRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getInputParams($field, $staff = true, $object = false)
    {
        $value = $object ? $object->getData($field->getCode()) : '';
        switch ($field->getType()) {
            case 'checkbox':
                $value = 1;
                break;
            case 'date':
                if ($value == '0000-00-00 00:00:00') {
                    $value = time();
                }
                break;
        }

        return [
            'label'        => __($field->getName()),
            'name'         => $field->getCode(),
            'required'     => $staff ? $field->getIsRequiredStaff() : $field->IsCustomerRequired(),
            'value'        => $value,
            'checked'      => $object ? $object->getData($field->getCode()) : false,
            'values'       => $field->getValues(),
            'note'         => $field->getDescription(),
            'date_format'  => $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processPost($post, $object)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addFilter('is_editable_customer', true)
            ->addSortOrder($this->getSortOrder())
        ;

        $collection = $this->fieldRepository->getList($searchCriteria->create())->getItems();
        foreach ($collection as $field) {
            if (isset($post[$field->getCode()])) {
                $value = $post[$field->getCode()];
                $object->setData($field->getCode(), $value);
            }
            if ($field->getType() == 'checkbox') {
                if (!isset($post[$field->getCode()])) {
                    $object->setData($field->getCode(), 0);
                }
            } elseif ($field->getType() == 'date') {
                $value = $object->getData($field->getCode());
                try {
                    $value = $this->localeDate->formatDate($value, \IntlDateFormatter::SHORT);
                } catch (\Exception $e) { //we have exception if input date is in incorrect format
                    $value = '';
                }
                $object->setData($field->getCode(), $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue($rma, $field)
    {
        $value = $rma->getData($field->getCode());
        if (!$value) {
            return false;
        }
        if ($field->getType() == 'checkbox') {
            $value = $value ? __('Yes') : __('No');
        } elseif ($field->getType() == 'date') {
                $value = $this->localeDate->formatDate($value, \IntlDateFormatter::MEDIUM);
        } elseif ($field->getType() == 'select') {
            $values = $field->getValues();
            $value = $values[$value];
        }

        return $value;
    }

        /**
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    public function getAddressHtml(\Lof\Rma\Api\Data\RmaInterface $rma)
    {
        $returnAddress = $rma->getReturnAddress();
        if (!$returnAddress) {
            $returnAddress = $this->helper->getConfig($rma->getStoreId(),'rma/general/return_address');
        }
        return nl2br($returnAddress);
    }



    /**
     * @return \Lof\Rma\Model\ResourceModel\OrderStatusHistory\Collection
     */
    public function getAllowOrderId()
    {
        $allowedStatuses = $this->helper->getConfig($store = null,'rma/policy/allow_in_statuses');
        $allowedStatuses = explode(',', $allowedStatuses);

        $returnPeriod    = (int)$this->helper->getConfig($store = null,'rma/policy/return_period');

        /** @var \Lof\Rma\Model\ResourceModel\OrderStatusHistory\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->removeAllFieldsFromSelect()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('status', ['in' => $allowedStatuses])
            ->addFieldToFilter(
                new \Zend_Db_Expr('ADDDATE(created_at, '.$returnPeriod.')'),
                ['gt' => new \Zend_Db_Expr('NOW()')]
            )
        ;

        return $collection->getColumnValues('order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus($rma)
    {
        return $this->statusRepository->getById($rma->getStatusId());
    }

    /**
     * {@inheritdoc}
     */
    public function RmaReasonCount(RmaInterface $rma, $reasonId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.reason_id')
                    ->setValue($reasonId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * {@inheritdoc}
     */
    public function RmaConditionCount(RmaInterface $rma, $conditionId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.condition_id')
                    ->setValue($conditionId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * {@inheritdoc}
     */
    public function RmaResolutionCount(RmaInterface $rma, $resolutionId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.resolution_id')
                    ->setValue($resolutionId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    public function validate($data)
    {
        //return $this->validateRequireEntry($data) && $this->validateItemsQty($data);
        return $this->validateRequireEntry($data) && $this->validateItemsQty($data) && $this->validateFileUpload() ;
    }


    public function validateFileUpload()
    {
        if(empty($_FILES['return_label']['name'])) return true;

        $size_limit  = $this->helper->getConfig( null,'rma/general/file_size_limit');
        $allowed_ext = $this->helper->getConfig( null,'rma/general/file_allowed_extensions');
        $f_name = $_FILES['return_label']['name'];
        $f_name = explode('.',$f_name);
        $f_ext = end($f_name);
        $f_size = $_FILES['return_label']['size'];

        if(!empty($size_limit) && $f_size>$size_limit*1024*1024 ){
            $this->messageManager->addError(
                __("Please, check file size. file size should be equal or less than ").$size_limit.'MB'
            );
            return false;
        }

        if(count($allowed_ext)>1 && !in_array($f_ext,explode(',',$allowed_ext))){
            $this->messageManager->addError(
                __("Please, check file type. File extension should be among ").join(',',$allowed_ext)
            );
            return false;
        }

        return true;
    }






    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'items' => __('Items'),
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addError(
                    __('To apply changes you should fill in required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }

    /**
     * Check if any item has qty > 0
     *
     * @param array $data
     * @return bool
     */
    public function validateItemsQty(array $data)
    {
        $isEmpty = true;
        foreach ($data['items'] as $item) {
            if ((int)$item['qty_requested'] > 0) {
                $isEmpty = false;
                break;
            }
        }
        if ($isEmpty) {
            $this->messageManager->addError(
                __("Please, add order items to the RMA (set 'Qty to Return')")
            );
            return false;
        }
        return true;
    }

    /**
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    public function generateIncrementId(\Lof\Rma\Api\Data\RmaInterface $rma)
    {
        $id = $rma->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($rma->getOrderId());
        $result =  $order->getIncrementId();
        $result .= '-' .$id;

        return $result;
    }

    public function CheckFile($type , $size)
    {
        $allowedFiles =$this->helper->getConfig($store = null,'rma/general/file_allowed_extensions');
         $allowedFiles = explode(',',  $allowedFiles);
         $allowedFiles = array_map('trim',  $allowedFiles);
         $SizeLimit = $this->helper->getConfig($store = null,'rma/general/file_size_limit') * 1024 * 1024;
         if (count($allowedFiles)) {
                $exit = 0;
                foreach ($allowedFiles as $allowedType) {
                   if(strcmp($allowedType,$type)==0){
                      $exit = 1;
                   }
                }
                if($exit = 0){
                    return false;
                }
            }

            if ($SizeLimit && $size > $SizeLimit) {
                return false;
            }
        return true;
    }



}