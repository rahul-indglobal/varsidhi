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



namespace Lof\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class PrintLabel extends \Lof\Rma\Controller\Rma
{
    public function __construct(
        \Lof\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lof\Rma\Helper\Data                            $datahelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rmaRepository        = $rmaRepository;
        $this->registry             = $registry;
        $this->datahelper           = $datahelper;
        parent::__construct($customerSession, $context);
    }

    

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $id = $this->getRequest()->getParam('id');
        $rma = $this->rmaRepository->getById($id);
        if (!$rma) {
            return $resultRedirect->setPath('/');
        }
        $attachments = $this->datahelper->getAttachments('return_label',$rma->getId());
        if ($label = array_shift($attachments)) {
            return $resultRedirect->setPath('*/attachment/download',['uid' => $label->getUid()]);
        } else {
            $this->_forward('no_rote');
        }
    }
}
