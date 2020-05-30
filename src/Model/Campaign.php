<?php

namespace Admin\Mailjet\Model;

/**
 * https://dev.mailjet.com/email-api/v3/campaign/
 */
class Campaign
{
    const FROMEMAIL_KEY = 'FromEmail';
    //Mandatory properties (must be set through constructor)
    protected $fromEmail;
    //Optional properties (array)
    protected $optionalProperties = null;
    /**
     * @param string $fromEmail
     * @param array|null $optionalProperties
     */
    public function __construct($fromEmail, $optionalProperties = null)
    {
        $this->fromEmail = $fromEmail;
        $this->optionalProperties = $optionalProperties;
    }
    /**
     * Format Campaign for MailJet API request
     * @return array
     */
    public function format()
    {
        /*         * Add the mandatary props* */
        $result = array();
        if (!is_null($this->fromEmail)) {
            $result[self::FROMEMAIL_KEY] = $this->fromEmail;
        }
        /*         * Add the optional props if any* */
        if (!is_null($this->optionalProperties)) {
            $result = array_merge($result, $this->optionalProperties);
        }
        return $result;
    }
    /**
     * Correspond to properties in MailJet request
     * Array ['PropertyName' => value, ...]
     */
    public function getOptionalProperties()
    {
        return $this->optionalProperties;
    }
    /**
     * Set array of Campaign properties
     * @param array $properties
     * @return array
     */
    public function setOptionalProperties(array $properties)
    {
        $this->optionalProperties = $properties;
        return $this->optionalProperties;
    }
    /**
     * Add a new $property to Campaign
     * @param array $property
     * @return array
     */
    public function addOptionalProperty(array $property)
    {
        $this->optionalProperties[] = $property;
        return $this->optionalProperties;
    }
    /**
     * Remove a $property from Campaign
     * @param array $property
     * @return array
     */
    public function removeOptionalProperty(array $property)
    {
        foreach (array_keys($this->optionalProperties, $property) as $key) {
            unset($this->optionalProperties[$key]);
        }
        return $this->optionalProperties;
    }
}
