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
namespace Lof\Rma\Block\Adminhtml\Report;

class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
    * banner factory
    * @var \Magenhub\Chris\Model\ChrisFactory
    */
    protected $_chrisFactory;

    /**
    * Registry object
    * @var \Magento\Framework\Registry
    */
    protected $_coreRegistry;

    /**
    * [__construct description]
    * @param \Magento\Backend\Block\Template\Context $context [description]
    * @param \Magento\Backend\Helper\Data $backendHelper [description]
    * @param \Magenhub\Chris\Model\ChrisFactory $chrisFactory [description]
    * @param \Magento\Framework\Registry $coreRegistry [description]
    * @param array $data [description]
    */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
    \Magento\Backend\Helper\Data $backendHelper, 
    \Lof\Rma\Model\ResourceModel\Item\Collection $RmaFactory, 
    \Lof\Rma\Model\ResourceModel\Status\Collection $StatusFactory, 
    \Magento\Framework\Registry $coreRegistry,
     array $data = []
    ) {
        $this->_RmaFactory = $RmaFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_StatusFactory = $StatusFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('lofGrid');
        $this->setSaveParametersInSession(false);
         $this->setFilterVisibility(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        if($params = $this->getRequest()->getParams()){
            $type= isset($params['type'])?$params['type']:'month';
            $params['to'] = isset($params['to'])?$params['to']:date("Y-m-d");
            $params['from'] = isset($params['from'])?$params['from']:date("Y-m-d",strtotime("-1 month"));
            $endate =str_replace( '.', '-', $params['to'] ); 
            $startdate =str_replace( '.', '-', $params['from'] ); 
        }
        else{
            $type= 'month';
            $startdate = date("Y-m-d",strtotime("-1 month"));
            $endate = date("Y-m-d");
        }
        $collection = $this->_RmaFactory
            ->addFieldToFilter(
                    'main_table.reason_id',
                    array('notnull' => true)
                )
            ->setPeriodType($type)
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($startdate)
            ->addDateToFilter( $endate)
            ->_getSelectedColumns();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
    * @return $this
    */
    protected function _prepareColumns() {
        $this->addColumn(
        'chris_id', [
        'header' => __('Time'),
        'type' => 'text',
        'index' => 'time',
        'header_css_class' => 'col-id',
        'column_css_class' => 'col-id',
        'width' => '30px',
        ]
        );
         $this->addColumn(
        'item', [
        'header' => __('Item'),
        'index' => 'product_name',
        'class' => 'xxx',
        'width' => '50px',

        ]
        );
        $this->addColumn(
        'rma', [
        'header' => __('Total Rma'),
        'index' => 'total_rma_cnt',
        'class' => 'xxx',
        'width' => '50px',
        ]
        );
         $this->addColumn(
        'request', [
        'header' => __('Total Request'),
        'index' => 'total_requested_cnt',
        'class' => 'xxx',
        'width' => '50px',
        ]
        );

        $this->addColumn(
        'return', [
        'header' => __('Total Return'),
        'index' => 'total_returned_cnt',
        'class' => 'xxx',
        'width' => '50px',
        ]
        );

        /*$status = $this->_StatusFactory->getData();
        foreach ($status as $value) {
             $this->addColumn(
        $value['name'], [
        'header' => __($value['name']),
        'class' => 'xxx',
        'width' => '100px',
        'index' => $value['status_id'].'_cnt'
        ]
        );
        }*/
       
        
        
     $this->addExportType('*/Report/ExportCsvPR', __('CSV'));
        $this->addExportType('*/Report/ExportExcelPR', __('Excel'));

        return parent::_prepareColumns();
    }

    /**/

}
?>