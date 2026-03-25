<?php

namespace Custom\PincodeChecker\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_Custom_pincodechecker = $setup->getConnection()->newTable($setup->getTable('pincode_checker'));

        
        $table_Custom_pincodechecker->addColumn(
            'pincode_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Pincode ID'
        );
        

        
        $table_Custom_pincodechecker->addColumn(
            'pincode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'pincode'
        );
        
         $table_Custom_pincodechecker->addColumn(
            'delivery_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'delivery'
        );
        
         $table_Custom_pincodechecker->addColumn(
            'cod',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'cash on delivery'
        );

        $table_Custom_pincodechecker->addColumn(
            'shipping',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'shipping'
        );
        $setup->getConnection()->createTable($table_Custom_pincodechecker);

        $setup->endSetup();

        /* Create new pincode text attribute of product */

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pincode',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Exclude Pin code',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

    }
}
