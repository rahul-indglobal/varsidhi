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


namespace Lof\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Lof\Rma\Controller\Adminhtml\Rma;

class Save extends Rma
{
    protected $searchCriteriaBuilder;
    protected $attachmentRepository;

    public function __construct(
        \Lof\Rma\Helper\Data                                    $dataHelper,
        \Lof\Rma\Model\RmaFactory                               $rmaFactory,
        \Lof\Rma\Model\ItemFactory                              $itemFactory,
        \Lof\Rma\Model\AttachmentFactory                       $AttachmentFactory,
        \Magento\Sales\Model\OrderFactory                       $orderFactory,
        \Magento\Framework\Event\ManagerInterface               $eventManager,
        \Lof\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lof\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Magento\Framework\Registry                             $registry,
        \Magento\Framework\Api\SearchCriteriaBuilder          $searchCriteriaBuilder,
        \Lof\Rma\Model\AttachmentRepository          $attachmentRepository,
        \Magento\Backend\App\Action\Context                     $context
    ) {
        $this->rmaFactory           = $rmaFactory;
        $this->itemFactory          = $itemFactory;
        $this->orderFactory         = $orderFactory;
        $this->attachmentFactory  = $AttachmentFactory;
        $this->messageRepository     = $messageRepository;
        $this->rmaRepository         = $rmaRepository;
        $this->eventManager         = $eventManager;
        $this->registry             = $registry;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attachmentRepository = $attachmentRepository;
        $this->dataHelper          = $dataHelper;

        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {

            if (!$this->dataHelper->validate($data)) {
                return $resultRedirect->setPath('*/*/add',
                    ['order_id' => $data['order_id'], '_current' => true]);
            }
            try {
               $user = $this->_auth->getUser();
             
                $rmadata =  $data;
                unset($rmadata['items']);

                if (empty($rmadata['return_address'])) {
                    unset($rmadata['return_address']);
                }
                $itemdata = $data['items'];
                 foreach ( $itemdata as $k => $item) {
                    if (!(int) $item['reason_id']) {
                        unset($item['reason_id']);
                    }
                    if (!(int) $item['resolution_id']) {
                        unset($item['resolution_id']);
                    }
                    if (!(int) $item['condition_id']) {
                        unset($item['condition_id']);
                    }
                    $itemdata[$k] = $item;
                }      
                $rma = $this->rmaFactory->create();
                if (isset($rmadata['rma_id']) && $rmadata['rma_id']) {
                    $rma->load($data['rma_id']);
                }
                unset($rmadata['rma_id']);



                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderFactory->create()->load((int) $rmadata['order_id']);

               

                $rma->setCustomerId($order->getCustomerId());
                $rma->setStoreId($order->getStoreId());
                
                if (!$rma->getUserId()) {
                    $rma->setUserId($user->getId());
                }

                $rma->addData($rmadata);

                //Delete attachment if requested
                if ($candidate = $rma->getData('return_label')) {
                    if(isset($candidate['delete']) && $candidate['delete']=='1'){
                        $searchCriteria = $this->searchCriteriaBuilder
                            ->addFilter('item_id', $rma->getRmaId())
                            ->addFilter('item_type', 'return_label')
                            ->addFilter('name', $candidate['value'])
                        ;
                        $items4Delete = $this->attachmentRepository->getList($searchCriteria->create())->getItems();
                        foreach ($items4Delete as $i4d) {
                            $this->attachmentRepository->delete($i4d);
                        }
                    }
                }

                $rma->save();

                $this->registry->register('current_rma', $rma);

                 
                 foreach ($itemdata as $item) {
                    $Items = $this->itemFactory->create();
                    if (isset($item['item_id']) && $item['item_id']) {
                        $Items->load((int) $item['item_id']);
                    }
                    unset($item['item_id']);
                    $Items->addData($item)
                        ->setRmaId($rma->getId());
                    $Items->save();
                }

              
                if (
                    (isset($data['reply']) && $data['reply'] != '') ||
                    (!empty($_FILES['attachment']) && !empty($_FILES['attachment']['name'][0]))
                ) {
                      $message = $this->messageRepository->create();
                       $message->setRmaId($rma->getId())
                            ->setText($data['reply'], false);
                        if (isset($data['internalcheck']) && $data['internalcheck'] == '1') {
                                   $message->setIsVisibleInFrontend(false)
                                    ->setIsCustomerNotified(false)
                                    ->setUserId($user->getId());
                             }
                        else{
                                $message->setIsVisibleInFrontend(true)
                                    ->setIsCustomerNotified(true)
                                    ->setUserId($user->getId());
                            }
                            $this->messageRepository->save($message);

                            $rma->setLastReplyName($user->getName())
                                ->setIsAdminRead($user instanceof \Magento\User\Model\User);
                            $this->rmaRepository->save($rma);

                            $this->eventManager->dispatch(
                                'rma_add_message_after',
                                ['rma'=> $rma, 'message' => $message, 'user' => $user, 'params' => $data]
                            );
                }

                
                $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'user' => $user]);
      

                $this->messageManager->addSuccess(__('RMA was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rma->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                if ($this->getRequest()->getParam('id')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                } else {
                    return $resultRedirect->setPath(
                        '*/*/add',
                        ['order_id' => $this->getRequest()->getParam('order_id')]
                    );
                }
            }
        }
        $this->messageManager->addError(__('Unable to find rma to save'));

        return $resultRedirect->setPath('*/*/');
    }
}
