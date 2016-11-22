<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use Paysera\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;

class ContextAwareNormalizerFactory
{

    /**
     * @var \Paysera\Component\Serializer\Filter\FieldsFilter
     */
    protected $fieldsFilter;

    /**
     * @var \Paysera\Component\Serializer\Filter\FieldsParser
     */
    protected $fieldsParser;

    public function __construct(
        FieldsParser $fieldsParser,
        FieldsFilter $fieldsFilter
    ) {
        $this->fieldsParser = $fieldsParser;
        $this->fieldsFilter = $fieldsFilter;
    }

    /**
     * @param DenormalizerInterface|NormalizerInterface $normalizer
     *
     * @return ContextAwareNormalizerInterface
     */
    public function create($normalizer)
    {
        return new DistributedNormalizer(
            $this,
            $this->fieldsParser,
            $this->fieldsFilter,
            $normalizer
        );
    }
} 
