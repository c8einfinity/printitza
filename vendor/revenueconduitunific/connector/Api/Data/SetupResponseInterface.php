<?php

namespace Unific\Connector\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface SetupResponseInterface extends ExtensibleDataInterface
{
    /**
     * @return \Unific\Connector\Api\Data\HmacInterface
     */
    public function getHmac();

    /**
     * @param \Unific\Connector\Api\Data\HmacInterface
     * @return void
     */
    public function setHmac(\Unific\Connector\Api\Data\HmacInterface $hmacInterface);

    /**
     * @return \Unific\Connector\Api\Data\TotalsInterface
     */
    public function getTotals();

    /**
     * @param TotalsInterface $totalsInterface
     * @return void
     */
    public function setTotals(\Unific\Connector\Api\Data\TotalsInterface $totalsInterface);
}
