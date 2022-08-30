<?php


namespace Ajay\Note\Model;

use Ajay\Note\Api\Data\NoteInterface;

class Note extends \Magento\Framework\Model\AbstractModel implements NoteInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ajay\Note\Model\ResourceModel\Note');
    }

    /**
     * Get note_id
     * @return string
     */
    public function getNoteId()
    {
        return $this->getData(self::NOTE_ID);
    }

    /**
     * Set note_id
     * @param string $noteId
     * @return \Ajay\Note\Api\Data\NoteInterface
     */
    public function setNoteId($noteId)
    {
        return $this->setData(self::NOTE_ID, $noteId);
    }

    /**
     * Get customer_id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Ajay\Note\Api\Data\NoteInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Ajay\Note\Api\Data\NoteInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Ajay\Note\Api\Data\NoteInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }
}
