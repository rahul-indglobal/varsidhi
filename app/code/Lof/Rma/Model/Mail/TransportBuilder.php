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


namespace Lof\Rma\Model\Mail;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Mail\Template\FactoryInterface;
use \Magento\Framework\Mail\Template\SenderResolverInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory);

    }

    /**
     * @param string $body
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param string $filename
     * @return $this
     */
    public function addAttachment(
        $body,
        $mimeType    = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding    = \Zend_Mime::ENCODING_BASE64,
        $filename    = null
    ) {
        if(method_exists($this->message,'createAttachment')){
            $this->message->createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        }else{
            $attachment = new \Zend\Mime\Part($body);
            $attachment->type = $mimeType;
            $attachment->disposition = $disposition;
            $attachment->encoding = $encoding;
            $attachment->filename = $filename;  
        }
        return $this;
    }

}