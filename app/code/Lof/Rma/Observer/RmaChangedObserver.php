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


namespace Lof\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lof\Rma\Api\Repository\StatusRepositoryInterface;

class RmaChangedObserver implements ObserverInterface
{
    public function __construct(
       \Lof\Rma\Helper\RuleHelper     $ruleHelper,
       \Lof\Rma\Helper\Data     $rmaHelper,
       \Lof\Rma\Model\AttachmentFactory $AttachmentFactory,
       \Magento\Framework\Api\SortOrderBuilder                       $sortOrderBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder                  $searchCriteriaBuilder, 
        \Lof\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        StatusRepositoryInterface     $statusRepository,
        \Lof\Rma\Helper\Mail          $rmaMail
    ) {
        $this->ruleHelper           =  $ruleHelper;
         $this->rmaHelper           =  $rmaHelper;
        $this->sortOrderBuilder      = $sortOrderBuilder;       
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attachmentFactory  = $AttachmentFactory;
        $this->messageRepository     = $messageRepository;
        $this->statusRepository     = $statusRepository;
        $this->rmaMail              = $rmaMail;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rma = $observer->getData('rma');
        $attachments = $this->rmaHelper->getAttachments('return_label', $rma->getId());
        $attachment = array_shift($attachments);
            if (!$attachment) {
              $attachment = $this->attachmentFactory->create();

            }

            if ((isset($_FILES['return_label']['name'][0]) && $_FILES['return_label']['name'][0] != '')) {
                               
                if (isset($_POST['return_label']['delete']) && $_POST['return_label']['delete'])
                {
                         $attachment->delete();
                }
                 $content = @file_get_contents(addslashes( $_FILES['return_label']['tmp_name']));
                 $type = $_FILES['return_label']['type'];
                 $size = $_FILES['return_label']['size'];
                 $check = $this->rmaHelper->CheckFile($type, $size);
                 if($check){
                    $attachment
                    ->setItemType('return_label')
                    ->setItemId($rma->getId())
                    ->setName( $_FILES['return_label']['name'])
                    ->setSize( $size)
                    ->setBody($content)
                    ->setType($type)
                    ->save();         
                 }
                 
                 
                                
    }
      
        
        

        $this->notifyRmaChange($rma, $observer->getData('user'));
    }


    public function notifyRmaChange($rma, $user)
    {
        $status = $this->statusRepository->getById($rma->getStatusId());

        if ($rma->getOrigData('status_id') == NULL || $rma->getStatusId() != $rma->getOrigData('status_id')) {
            
            $this->onRmaStatusChange($rma, $user);
        }
        if ($rma->getOrigData('rma_id')) {
            if (
                $rma->getUserId() != $rma->getOrigData('user_id') //&&
                //$this->statusRepository->getAdminMessageForStore($status, $rma->getStoreId())
            ) {
                $this->onRmaUserChange($rma);
            }
            $this->ruleHelper->newEvent(
                'rma_updated', $rma
            );
        } else {
            $this->ruleHelper->newEvent(
                'rma_created', $rma
            );
        }
    }

    public function onRmaStatusChange($rma, $user)
    {

        $status = $this->statusRepository->getById($rma->getStatusId());
        $historyMessage  = $status->getHistoryMessage();
        $customerMessage = $status->getCustomerMessage();
        $adminMessage    = $status->getAdminMessage();
       
        if ($historyMessage[0]) {
            $text = $this->rmaMail->parseVariables($historyMessage[0], $rma);
            
            $params = [
                'isNotified' => $status->getCustomerMessage() != '',
                'isVisible'  => 1
            ];
            
            $message = $this->messageRepository->create();
            $message->setRmaId($rma->getId())
                    ->setText($text)
                    ->setIsVisibleInFrontend(true)
                    ->setIsCustomerNotified(true)
                    ->setUserId(1);
            
            $this->messageRepository->save($message);
            
            }
        if ($customerMessage[0]) {
            $this->rmaMail->sendNotificationCustomer($rma, $customerMessage[0], true);
        }
        if ($adminMessage[0]) {
            $this->rmaMail->sendNotificationAdmin($rma, $adminMessage[0], true);
        }

      
        if ($customerMessage || $historyMessage) {
            if ($rma->getUserId()) {
                $rma->setLastReplyName($this->rmaHelper->getUserName($rma->getUserId()))
                    ->save();
            }
        }
    }

    /**
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     *
     * @return void
     */
    protected function onRmaUserChange($rma)
    {
        $status  = $this->rmaHelper->getStatus($rma);
        //$message = $this->statusRepository->getAdminMessageForStore($status, $rma->getStoreId());
        $message = '';
        $message = $this->rmaMail->parseVariables($message, $rma);
        $this->rmaMail->sendNotificationAdmin($rma, $message);
    }
}