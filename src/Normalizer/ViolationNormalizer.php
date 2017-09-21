<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\Violation;

class ViolationNormalizer extends BaseDenormalizer implements NormalizerInterface
{
    /**
     * @param array $data
     *
     * @return Violation
     */
    public function mapToEntity($data)
    {
        $violation = new Violation();

        if (isset($data['code'])) {
            $violation->setCode($data['code']);
        }

        if (isset($data['message'])) {
            $violation->setMessage($data['message']);
        }

        if (isset($data['field'])) {
            $violation->setField($data['field']);
        }

        return $violation;
    }

    /**
     * @param Violation $entity
     *
     * @return array
     */
    public function mapFromEntity($entity)
    {
        $data = array();
        if ($entity->getCode() !== null) {
            $data['code'] = $entity->getCode();
        }
        if ($entity->getMessage() !== null) {
            $data['message'] = $entity->getMessage();
        }
        if ($entity->getField() !== null) {
            $data['field'] = $entity->getField();
        }
        return $data;
    }
}
