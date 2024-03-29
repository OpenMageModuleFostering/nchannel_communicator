<?php

class Nchannel_Communicator_Model_Api2_ Renderer_Json implements Mage_Api2_Model_Renderer_Json
{
    /**
     * Adapter mime type
     */
    const MIME_TYPE = 'application/json';

    /**
     * Convert Array to JSON
     *
     * @param array|object $data
     * @return string
     */
    public function render($data)
    {
        return Zend_Json::encode($data);
    }

    /**
     * Get MIME type generated by renderer
     *
     * @return string
     */
    public function getMimeType()
    {
        return self::MIME_TYPE;
    }
}
