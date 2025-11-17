<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Accessor\FieldAccessorInterface;
use Paysera\Component\Serializer\Entity\NormalizationContextInterface;
use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;

class DistributedNormalizer implements DenormalizerInterface, ContextAwareNormalizerInterface
{
    /**
     * @var ContextAwareNormalizerFactory
     */
    protected $factory;

    /**
     * @var \Paysera\Component\Serializer\Filter\FieldsFilter
     */
    protected $fieldsFilter;

    /**
     * @var \Paysera\Component\Serializer\Filter\FieldsParser
     */
    protected $fieldsParser;

    /**
     * @var DenormalizerInterface|NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var FieldAccessorInterface[]
     */
    protected $fieldAccessors = array();

    /**
     * @var array of boolean
     */
    protected $fieldDefault = array();

    /**
     * @var DenormalizerInterface[]|NormalizerInterface[]
     */
    protected $fieldNormalizers = array();

    /**
     * @param \Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory $factory
     * @param \Paysera\Component\Serializer\Filter\FieldsParser                   $fieldsParser
     * @param \Paysera\Component\Serializer\Filter\FieldsFilter                   $fieldsFilter
     * @param DenormalizerInterface|NormalizerInterface                       $normalizer
     */
    public function __construct(
        ContextAwareNormalizerFactory $factory,
        FieldsParser $fieldsParser,
        FieldsFilter $fieldsFilter,
        $normalizer
    ) {
        $this->factory = $factory;
        $this->fieldsParser = $fieldsParser;
        $this->fieldsFilter = $fieldsFilter;
        $this->normalizer = $normalizer;
    }

    /**
     * @param string                                    $fieldName
     * @param FieldAccessorInterface                    $fieldAccessor
     * @param DenormalizerInterface|NormalizerInterface $normalizer
     */
    public function addField($fieldName, FieldAccessorInterface $fieldAccessor, $normalizer)
    {
        if ($normalizer instanceof NormalizerInterface && !$normalizer instanceof ContextAwareNormalizerInterface) {
            $normalizer = $this->factory->create($normalizer);
        }

        $this->fieldAccessors[$fieldName] = $fieldAccessor;
        $this->fieldNormalizers[$fieldName] = $normalizer;
        $this->fieldDefault[$fieldName] = true;
    }

    /**
     * @param string                                    $fieldName
     * @param FieldAccessorInterface                    $fieldAccessor
     * @param DenormalizerInterface|NormalizerInterface $normalizer
     */
    public function addAdditionalField($fieldName, FieldAccessorInterface $fieldAccessor, $normalizer)
    {
        $this->addField($fieldName, $fieldAccessor, $normalizer);
        $this->fieldDefault[$fieldName] = false;
    }

    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws \Paysera\Component\Serializer\Exception\InvalidDataException
     */
    public function mapToEntity($data)
    {
        $additional = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (
                    isset($this->fieldNormalizers[$key])
                    && $this->fieldNormalizers[$key] instanceof DenormalizerInterface
                ) {
                    $additional[$key] = $data[$key];
                    unset($data[$key]);
                }
            }
        }

        $entity = $this->normalizer->mapToEntity($data);
        foreach ($additional as $key => $value) {
            if ($value !== null) {
                $this->fieldAccessors[$key]->setValue($entity, $this->fieldNormalizers[$key]->mapToEntity($value));
            }
        }

        return $entity;
    }

    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param mixed $entity
     * @param null|NormalizationContextInterface $context
     *
     * @return mixed
     */
    public function mapFromEntity($entity, ?NormalizationContextInterface $context = null)
    {
        if ($this->normalizer instanceof ContextAwareNormalizerInterface) {
            $data = $this->normalizer->mapFromEntity($entity, $context);
        } else {
            $data = $this->normalizer->mapFromEntity($entity);
        }

        $fields = $context !== null ? $context->getFields() : null;
        $scope = $context !== null ? $context->getScope() : array();
        $data = $this->fieldsFilter->filter($data, $fields, $scope);

        $fieldsConfig = $this->fieldsParser->parseFields($fields, $scope);
        foreach ($this->fieldAccessors as $fieldName => $fieldAccessor) {
            if ($fieldsConfig->isIncluded($fieldName, $this->fieldDefault[$fieldName])) {
                $value = $fieldAccessor->getValue($entity);
                if ($value !== null) {
                    $data[$fieldName] = $this->fieldNormalizers[$fieldName]->mapFromEntity(
                        $value,
                        $context !== null ? $context->createScopedContext($fieldName) : null
                    );
                }
            }
        }
        return $data;
    }
}
