<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;

class DateNormalizer extends BaseDenormalizer implements NormalizerInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var \DateTimeZone
     */
    protected $remoteTimezone;

    public function __construct($format, $remoteTimezone = null)
    {
        $this->format = $format;
        $this->remoteTimezone = $remoteTimezone !== null ? $remoteTimezone : $this->getLocalTimezone();
    }

    /**
     * @param string $data
     * @throws InvalidDataException
     * @return \DateTime
     */
    public function mapToEntity($data)
    {
        $date = \DateTime::createFromFormat(
            $this->format,
            $data,
            $this->remoteTimezone
        );
        if ($date === false) {
            throw new InvalidDataException('Provided date format is invalid');
        }
        $date->setTimezone($this->getLocalTimezone());

        return $date;
    }

    /**
     * @param \DateTime $entity
     * @return string
     */
    public function mapFromEntity($entity)
    {
        if ($entity === null) {
            return null;
        }

        $entity = clone $entity;
        $entity->setTimezone($this->remoteTimezone);

        return $entity->format($this->format);
    }

    protected function getLocalTimezone()
    {
        return new \DateTimeZone(date_default_timezone_get());
    }
}
