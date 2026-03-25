<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Api;

use \Ideo\StoreLocator\Api\Data\StoreInterface;

/**
 * Interface StoreRepositoryInterface
 * @package Ideo\StoreLocator\Api
 */
interface StoreRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Ideo\StoreLocator\Api\Data\StoreInterface $model
     *
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     * @throws \Exception
     */
    public function save(StoreInterface $model);

    /**
     * @param \Ideo\StoreLocator\Api\Data\StoreInterface $model
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(StoreInterface $model);

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);
}
