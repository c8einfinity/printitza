<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface TrackInterface
 * @api
 */
interface TrackInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Returns entity id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface
     */
    public function setEntityId($entityId);

    /**
     * Returns orderticket entity id
     *
     * @return int
     */
    public function getOrderTicketEntityId();

    /**
     * Set orderticket entity id
     *
     * @param int $entityId
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface
     */
    public function setOrderTicketEntityId($entityId);

    /**
     * Returns track number
     *
     * @return string
     */
    public function getTrackNumber();

    /**
     * Set track number
     *
     * @param string $trackNumber
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface
     */
    public function setTrackNumber($trackNumber);

    /**
     * Returns carrier title
     *
     * @return string
     */
    public function getCarrierTitle();

    /**
     * Set carrier title
     *
     * @param string $carrierTitle
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface
     */
    public function setCarrierTitle($carrierTitle);

    /**
     * Returns carrier code
     *
     * @return string
     */
    public function getCarrierCode();

    /**
     * Set carrier code
     *
     * @param string $carrierCode
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface
     */
    public function setCarrierCode($carrierCode);

}
