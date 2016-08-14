<?php

namespace Staffim\DTOBundle\ODM;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\MongoDB\Query\Builder;
use Staffim\DTOBundle\MappingStorage\MappingStorageInterface;

class FieldsPrimer
{
    /**
     * @var \Staffim\DTOBundle\MappingStorage\MappingStorageInterface
     */
    private $mappingStorage;

    /**
     * @param \Staffim\DTOBundle\MappingStorage\MappingStorageInterface $mappingStorage
     */
    public function __construct(MappingStorageInterface $mappingStorage)
    {
        $this->mappingStorage = $mappingStorage;
    }

    /**
     * @param \Doctrine\MongoDB\Query\Builder $queryBuilder
     * @param \Doctrine\ODM\MongoDB\Mapping\ClassMetadata $classMetadata
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function primeFields(Builder $queryBuilder, ClassMetadata $classMetadata)
    {
        $relationsConfig = $this->getRelations();

        foreach ($relationsConfig->getChildrenKeys() as $field) {
            if (!$this->canPrimeField($classMetadata, $field)) {
                continue;
            }

            $queryBuilder->field($field)->prime($this->createFieldPrimer([$field]));
        }

        return $queryBuilder;
    }

    /**
     * @return \Staffim\DTOBundle\MappingStorage\Config
     */
    protected function getRelations()
    {
        return $this->mappingStorage->getRelations();
    }

    /**
     * @param array $path
     * @return \Closure
     */
    private function createFieldPrimer(array $path)
    {
        return function (DocumentManager $dm, ClassMetadata $class, array $ids, array $hints) use ($path) {
            $qb = $dm->createQueryBuilder($class->name)
                ->field($class->identifier)->in($ids);

            $relationsConfig = $this->getRelations();

            $primeFields = $relationsConfig->getChildrenKeys($path);

            foreach ($primeFields as $field) {
                $childPath = $path;
                $childPath[] = $field;

                if (!$this->canPrimeField($class, $field)) {
                    continue;
                }
                $qb->field($field)->prime($this->createFieldPrimer($childPath));
            }

            if (!empty($hints[Query::HINT_SLAVE_OKAY])) {
                $qb->slaveOkay(true);
            }

            if (!empty($hints[Query::HINT_READ_PREFERENCE])) {
                $qb->setReadPreference($hints[Query::HINT_READ_PREFERENCE], $hints[Query::HINT_READ_PREFERENCE_TAGS]);
            }

            $qb->getQuery()->execute()->toArray(false);
        };
    }

    /**
     * @param \Doctrine\ODM\MongoDB\Mapping\ClassMetadata $class
     * @param string $field
     * @return bool
     */
    private function canPrimeField(ClassMetadata $class, $field)
    {
        if (!array_key_exists($field, $class->fieldMappings)) {
            return false;
        }

        $mapping = $class->fieldMappings[$field];

        if (!isset($mapping['reference']) || !$mapping['isOwningSide']) {
            return false;
        }

        if (!isset($mapping['reference']) && empty($mapping['embedded'])) {
            return false;
        }

        return true;
    }
}
