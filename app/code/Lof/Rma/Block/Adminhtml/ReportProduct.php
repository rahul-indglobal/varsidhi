<?php

namespace Lof\Rma\Block\Adminhtml;


use Magento\Framework\View\Element\Template;

/**
 * Class FacebookSupport
 * @package Lof\FaceSupportLive\Block\Chatbox
 */
class ReportProduct extends Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * FacebookSupport constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $my_template = "report/rma/product/grid/chart.phtml";
        if($this->hasData("producttemplate") && $this->getData("producttemplate")) {
            $my_producttemplate = $this->getData("producttemplate");
        } elseif(isset($data['producttemplate']) && $data['producttemplate']){
            $my_producttemplate = $data['template'];
        }
        if($my_template) {
            $this->setTemplate($my_template);
        }
       
    }

}
   
     

   

    

