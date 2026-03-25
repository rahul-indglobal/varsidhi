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
use Lof\Rma\Model\Config;

use Magento\Framework\Event\ObserverInterface;

class AddMessageObserver implements ObserverInterface
{
    public function __construct(
       \Lof\Rma\Helper\Mail $rmaMail,
        \Lof\Rma\Helper\RuleHelper $ruleHelper,
        \Lof\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository,
        \Lof\Rma\Helper\Data $rmaHelper
    ) {
        $this->rmaMail               = $rmaMail;
        $this->ruleHelper            = $ruleHelper;
        $this->rmaHelper             = $rmaHelper;
        $this->attachmentRepository  = $attachmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $rma = $observer->getData('rma');

        $user = $observer->getData('user');

        $message = $observer->getData('message');

        $params = $observer->getData('params');
       
       
         
        
        if ( (isset($_FILES['attachment']['name'][0]) && $_FILES['attachment']['name'][0] != '')) {
            $i = 0;
        foreach ($_FILES['attachment']['name'] as $name) {
            if ($name == '' || !empty($_FILES['attachment'][$name]['is_saved'])) {
                continue;
            }
             $type = $_FILES['attachment']['type'][$i];
            $size = $_FILES['attachment']['size'][$i];
            $check = $this->rmaHelper->CheckFile($type, $size);
            if(!$check)
                continue;
            
            $attachment = $this->attachmentRepository->create();
             $content = @file_get_contents(addslashes($_FILES['attachment']['tmp_name'][$i]));
          
             $attachment
                ->setItemType('message')
                ->setItemId($message->getId())
                ->setName($name)
                ->setSize($size)
                ->setBody($content)
                ->setType($type)
                ->save();

            ++$i;
            $_FILES['attachment'][$name]['is_saved'] = 1;
        }
        }
        

        if ($user instanceof \Magento\User\Model\User) {
            if ($message->getIsCustomerNotified()) {
                $this->rmaMail->sendNotificationCustomer($rma, $message);
            }
            if ($rma->getUserId() != $user->getId() && !$message->getIsVisibleInFrontend()) {
                $this->rmaMail->sendNotificationAdmin($rma, $message);
            }
            $this->ruleHelper->newEvent(
                'new_customer_reply', $rma
            );
        } else {
            if (isset($params['isNotifyAdmin']) && $params['isNotifyAdmin']) {
                $this->rmaMail->sendNotificationAdmin($rma, $message);
            }
            if ($message->getIsCustomerNotified()) {
                $this->rmaMail->sendNotificationCustomer($rma, $message);
            }
            $this->ruleHelper->newEvent(
                'new_customer_reply', $rma
            );
        }
    }
}