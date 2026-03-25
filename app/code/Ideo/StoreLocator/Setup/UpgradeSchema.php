<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->renameUpdatedAtColumns($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function renameUpdatedAtColumns(SchemaSetupInterface $setup)
    {
        $categoriesTableName = $setup->getTable('ideo_storelocator_category');
        $storesTableName = $setup->getTable('ideo_storelocator_store');

        $connection = $setup->getConnection();
        $connection->changeColumn(
            $categoriesTableName,
            'updated_ad',
            'updated_at',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'length' => null,
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT,
                'comment' => 'Updated At'
            ]
        );

        $connection->changeColumn(
            $storesTableName,
            'updated_ad',
            'updated_at',
            [
                'type' => Table::TYPE_TIMESTAMP,
                'length' => null,
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT,
                'comment' => 'Updated At'
            ]
        );
    }
}
