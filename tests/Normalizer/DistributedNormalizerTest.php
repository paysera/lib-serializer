<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Entity\NormalizationContext;
use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use Paysera\Component\Serializer\Normalizer\PlainItemNormalizer;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use Paysera\Component\Serializer\Tests\Fixtures\PublicPropertyFieldAccessor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

class DistributedNormalizerTest extends TestCase
{
    /**
     * The three field maps default on the property declarations rather than in the
     * constructor, so they survive every instantiation path. A constructor assignment
     * would leave them null for instances built without one — doubles created with
     * disableOriginalConstructor(), or reflection hydration. Both readers degrade
     * silently on null rather than throwing: mapFromEntity() iterates $fieldAccessors,
     * which warns and skips, dropping every distributed field from the output, and
     * mapToEntity() probes isset($this->fieldNormalizers[$key]), which is simply false
     * for every key.
     *
     * @dataProvider fieldMapProvider
     */
    public function testFieldMapsDefaultToArrayWhenConstructorIsBypassed($property)
    {
        $normalizer = (new ReflectionClass(DistributedNormalizer::class))->newInstanceWithoutConstructor();

        $reflectionProperty = new ReflectionProperty(DistributedNormalizer::class, $property);
        $reflectionProperty->setAccessible(true);

        $this->assertSame([], $reflectionProperty->getValue($normalizer));
    }

    public function fieldMapProvider()
    {
        return [
            'fieldAccessors' => ['fieldAccessors'],
            'fieldDefault' => ['fieldDefault'],
            'fieldNormalizers' => ['fieldNormalizers'],
        ];
    }

    public function testMapToEntityHandsFieldKeysToFieldDenormalizers()
    {
        $entity = new stdClass();
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->once())->method('mapToEntity')->with(['name' => 'John'])->willReturn($entity);

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField('tags', new PublicPropertyFieldAccessor('tags'), new PlainNormalizer());
        $normalizer->addAdditionalField(
            'code',
            new PublicPropertyFieldAccessor('code'),
            new PlainItemNormalizer('value')
        );

        $this->assertSame(
            $entity,
            $normalizer->mapToEntity(['name' => 'John', 'tags' => ['a', 'b'], 'code' => ['value' => 'X1']])
        );
        $this->assertSame(['a', 'b'], $entity->tags);
        $this->assertSame('X1', $entity->code);
    }

    public function testMapToEntityRemovesNullFieldValueWithoutSettingIt()
    {
        $entity = new stdClass();
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->once())->method('mapToEntity')->with(['name' => 'John'])->willReturn($entity);

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField('tags', new PublicPropertyFieldAccessor('tags'), new PlainNormalizer());

        $normalizer->mapToEntity(['name' => 'John', 'tags' => null]);

        $this->assertSame([], get_object_vars($entity));
    }

    public function testMapToEntityLeavesKeyOfFieldWithoutDenormalizerInData()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->once())
            ->method('mapToEntity')
            ->with(['name' => 'John', 'summary' => 'text'])
            ->willReturn('entity');

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField(
            'summary',
            new PublicPropertyFieldAccessor('summary'),
            $this->createMock(ContextAwareNormalizerInterface::class)
        );

        $this->assertSame('entity', $normalizer->mapToEntity(['name' => 'John', 'summary' => 'text']));
    }

    public function testMapToEntityPassesDataThatIsNotArrayToInnerDenormalizer()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->once())->method('mapToEntity')->with('raw')->willReturn('entity');

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField('tags', new PublicPropertyFieldAccessor('tags'), new PlainNormalizer());

        $this->assertSame('entity', $normalizer->mapToEntity('raw'));
    }

    public function testMapFromEntityWithoutContextAddsDefaultFieldsOnly()
    {
        $entity = new stdClass();
        $entity->tags = ['a'];
        $entity->code = 'X1';
        $entity->empty = null;

        $inner = $this->createMock(NormalizerInterface::class);
        $inner->expects($this->once())->method('mapFromEntity')->with($entity)->willReturn(['name' => 'John']);

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField('tags', new PublicPropertyFieldAccessor('tags'), new PlainNormalizer());
        $normalizer->addField('empty', new PublicPropertyFieldAccessor('empty'), new PlainNormalizer());
        $normalizer->addAdditionalField('code', new PublicPropertyFieldAccessor('code'), new PlainNormalizer());

        $this->assertSame(['name' => 'John', 'tags' => ['a']], $normalizer->mapFromEntity($entity));
    }

    /**
     * Requested fields filter the inner normalizer's output, include additional fields, drop
     * unrequested ones, and reach nested fields through a context scoped to the field name.
     */
    public function testMapFromEntityWithContextFiltersFieldsAndScopesNestedFields()
    {
        $entity = new stdClass();
        $entity->tags = ['a'];
        $entity->owner = ['id' => 7, 'name' => 'Jane'];
        $entity->code = 'X1';
        $context = (new NormalizationContext())->setFields(['name', 'owner.id', 'code']);

        $inner = $this->createMock(ContextAwareNormalizerInterface::class);
        $inner->expects($this->once())
            ->method('mapFromEntity')
            ->with($entity, $this->identicalTo($context))
            ->willReturn(['name' => 'John', 'age' => 30]);

        $normalizer = $this->createDistributedNormalizer($inner);
        $normalizer->addField('tags', new PublicPropertyFieldAccessor('tags'), new PlainNormalizer());
        $normalizer->addField('owner', new PublicPropertyFieldAccessor('owner'), new PlainNormalizer());
        $normalizer->addAdditionalField('code', new PublicPropertyFieldAccessor('code'), new PlainNormalizer());

        $this->assertSame(
            ['name' => 'John', 'owner' => ['id' => 7], 'code' => 'X1'],
            $normalizer->mapFromEntity($entity, $context)
        );
        $this->assertSame([], $context->getScope());
    }

    private function createDistributedNormalizer($normalizer)
    {
        $fieldsParser = new FieldsParser();
        $fieldsFilter = new FieldsFilter($fieldsParser);

        return new DistributedNormalizer(
            new ContextAwareNormalizerFactory($fieldsParser, $fieldsFilter),
            $fieldsParser,
            $fieldsFilter,
            $normalizer
        );
    }
}
