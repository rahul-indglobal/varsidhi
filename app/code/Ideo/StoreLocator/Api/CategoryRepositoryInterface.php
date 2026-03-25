<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Api;

use \Ideo\StoreLocator\Api\Data\CategoryInterface;

/**
 * Interface CategoryRepositoryInterface
 * @package Ideo\StoreLocator\Api
 */
interface CategoryRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \Ideo\StoreLocator\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Ideo\StoreLocator\Api\Data\CategoryInterface $model
     *
     * @return \Ideo\StoreLocator\Api\Data\CategoryInterface
     * @throws \Exception
     */
    public function save(CategoryInterface $model);

    /**
     * @param \Ideo\StoreLocator\Api\Data\CategoryInterface $model
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(CategoryInterface $model);

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);
}
