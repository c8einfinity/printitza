<?php

namespace Unific\Connector\Model\Api\Data;

class SetupResponse implements \Unific\Connector\Api\Data\SetupResponseInterface
{
    /**
     * @var
     */
    protected $hmac;

    /**
     * @var
     */
    protected $totals;

    /**
     * @return \Unific\Connector\Api\Data\HmacInterface
     */
    public function getHmac()
    {
        return $this->hmac;
    }

    /**
     * @param \Unific\Connector\Api\Data\HmacInterface $hmac
     */
    public function setHmac(\Unific\Connector\Api\Data\HmacInterface $hmac)
    {
        $this->hmac = $hmac;
    }

    /**
     * @return \Unific\Connector\Api\Data\TotalsInterface
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @param \Unific\Connector\Api\Data\TotalsInterface $totals
     */
    public function setTotals(\Unific\Connector\Api\Data\TotalsInterface $totals)
    {
        $this->totals = $totals;
    }
}
