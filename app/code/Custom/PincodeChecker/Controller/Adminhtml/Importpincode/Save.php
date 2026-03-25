<?php

namespace Custom\PincodeChecker\Controller\Adminhtml\Importpincode;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $fileCsv,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Custom\PincodeChecker\Model\PincodecheckerFactory $pincodeFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $fileio,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        
        $this->_backendSession = $context->getSession();
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $fileSystem;
        $this->_moduleReader = $moduleReader;
        $this->_fileCsv = $fileCsv;
        $this->_storeManager = $storeManagerInterface;
        $this->_pincodeFactory = $pincodeFactory;
        $this->registry = $registry;
        $this->_logger = $logger;
        $this->_fileio = $fileio;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        $this->registry->register('isSecureArea', true);
        try {
         

            $filepath = $this->_uploadFileAndGetName();

            if ($filepath!='' && file_exists($filepath)) {
                   
                chmod($filepath, 0777);
              
                $data = $this->_fileCsv->getData($filepath);
                $pincodeModel = $this->_pincodeFactory->create();
                $catemodel = $this->_pincodeFactory->create();
               
                
                if (isset($data[0]) && !empty($data[0])) {
                   

                     $header = $data[0];
                      

                    $pincodekey = array_search('pincode', $header);

                    $pincodeidkey = array_search('pincode_id', $header);
                    
                  
                    
                    $alreadyexist = [];
                    $exist_pincode_code = [];
                    $exist_pincode_status = [];

                    $pincodeModel = $this->_pincodeFactory->create();
                    $pincodeCollection = $pincodeModel->getCollection();
                    //$existpincode = [];
                    foreach($pincodeCollection as $key => $value){
                    //$existpincode = $pin->getPincode(); 
                    $exist_pincode_code[$value->getPincodeId()] = $value->getPincode();
                    $exist_pincode_status[$value->getPincodeId()] = $value->getDeliveryStatus();

                    }                   
                          
                    unset($data[0]);
                    foreach ($data as $key => $pincodeitem) {                 
                            $cat_data = $this->_getKeyValue($pincodeitem, $header); 

                            if (isset($pincodekey) && ($pincodekey!='' || $pincodekey === 0)) { 
                               
                                        $pinitem = $this->_pincodeFactory->create();
                                        $collection =  $pinitem->getCollection();
                                        $collection->addFieldToSelect('pincode_id')->addFieldToFilter('pincode',$pincodeitem[0]);
                                        $id = [];
                                        foreach($collection as $pinid){
                                             $id = $pinid->getPincodeId();
                                        }
                                        if($id){ 
                                            $catemodel = $this->_pincodeFactory->create();
                                            $cateitem = $catemodel->load($id); 
                                            $cateitem->setPincode($pincodeitem[0]); 
                                            $cateitem->setDeliveryStatus($pincodeitem[1]);
                                            $cateitem->save(); 

                                            }
                                        else{ 
                                        $pinitem = $this->_pincodeFactory->create();
                                        $pinitem->setPincode($pincodeitem[0]); 
                                        $pinitem->setDeliveryStatus($pincodeitem[1]);
                                        $pinitem->save(); 
                                         
                                        }
                                     

                                }
                                else{
                                        $this->messageManager->addError('Data Not Found.');
                                        $resultRedirect->setPath('Custom_pincodechecker/*/edit');
                                        return $resultRedirect;
                                }
                            }

                          $this->messageManager->addSuccess(__('Pincode Import Successfully'));
                          $resultRedirect->setPath('Custom_pincodechecker/*/edit');
                          return $resultRedirect;

                       
                 } else {
                    $this->messageManager->addError('Data Not Found.');
                    $resultRedirect->setPath('Custom_pincodechecker/*/edit');
                    return $resultRedirect;
                }
            }
           
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->debug($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->_logger->debug($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
        }
        $resultRedirect->setPath(
            'Custom_pincodechecker/*/edit',
            [
                '_current' => true
            ]
        );
        return $resultRedirect;
    }
    protected function _uploadFileAndGetName()
    {
        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
        $uploader->setAllowedExtensions(['CSV', 'csv']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
        ->getAbsolutePath('categoryimport');

        if (!is_dir($path)) {
            $this->_fileio->mkdir($path, '0777', true);
            $this->_fileio->chmod($path, '0777', true);
        }
        $result = $uploader->save($path.'/');
        if (isset($result['file']) && !empty($result['file'])) {
            return $result['path'].$result['file'];
        }
        return false;
    }

    protected function _getKeyValue($row, $headerArray)
    {
        $temp = [];
       foreach ($headerArray as $key => $value) {
           
                $temp[$value] = $row[$key];
          
        }
       return $temp;

        
    }

}
