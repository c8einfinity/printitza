<?php
namespace Designnbuy\Productattach\Api;

interface ProductattachInterface
{
    /**
     * Update / insert attachment
     * @param \Designnbuy\Productattach\Api\Data\ProductattachTableInterface $productattachTable
     * @param string $filename
     * @param string $fileContent
     * @return int
     */
    public function UpdateInsertAttachment(
        \Designnbuy\Productattach\Api\Data\ProductattachTableInterface $productattachTable,
        $filename,
        $fileContent
    );

    /**
     * Delete the attachment
     * @param int $int
     * @throws NotFoundException
     * @throws \Exception
     * @return bool
     */
    public function DeleteAttachment(
        $int
    );

    /**
     * Get attachment
     * @param int $int
     * @throws NotFoundException
     * @throws \Exception
     * @return \Designnbuy\Productattach\Api\Data\ProductattachTableInterface
     */
    public function GetAttachment(
        $int
    );

}