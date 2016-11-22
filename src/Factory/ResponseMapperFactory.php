<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Normalizer\NormalizerInterface;

class ResponseMapperFactory implements ResponseMapperFactoryInterface
{
    const MAPPER_OPTION = 'mapper';

    /**
     * @var NormalizerInterface
     */
    protected $defaultMapper;

    /**
     * @var NormalizerInterface[]
     */
    protected $mappers;


    public function __construct(NormalizerInterface $defaultMapper)
    {
        $this->defaultMapper = $defaultMapper;
        $this->mappers = array();
    }

    /**
     * @param string              $key
     * @param NormalizerInterface $mapper
     *
     * @return $this
     */
    public function addMapper($key, NormalizerInterface $mapper)
    {
        $this->mappers[$key] = $mapper;
        return $this;
    }

    /**
     * @param array $options
     *
     * @throws \RuntimeException
     * @return NormalizerInterface
     */
    public function createResponseMapper(array $options)
    {
        $key = isset($options[self::MAPPER_OPTION]) ? $options[self::MAPPER_OPTION] : null;
        if ($key !== null && !isset($this->mappers[$key])) {
            throw new \RuntimeException('Wrong mapper key specified: ' . $key);
        }
        if ($key === null) {
            foreach ($options as $optionKey => $value) {
                if ($value === true && isset($this->mappers[$optionKey])) {
                    $key = $optionKey;
                }
            }
        }
        return $key !== null ? $this->mappers[$key] : $this->defaultMapper;
    }
} 
