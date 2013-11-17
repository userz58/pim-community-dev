<?php

namespace Oro\Bundle\EntityBundle\Provider;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityBundle\ORM\EntityClassResolver;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;
use Oro\Bundle\EntityBundle\Exception\InvalidEntityException;

class EntityFieldProvider
{
    /**
     * @var ConfigProvider
     */
    protected $entityConfigProvider;

    /**
     * @var EntityClassResolver
     */
    protected $entityClassResolver;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    /**
     * Constructor
     *
     * @param ConfigProvider      $entityConfigProvider
     * @param EntityClassResolver $entityClassResolver
     * @param ManagerRegistry     $doctrine
     * @param EntityProvider      $entityProvider
     */
    public function __construct(
        ConfigProvider $entityConfigProvider,
        EntityClassResolver $entityClassResolver,
        ManagerRegistry $doctrine,
        EntityProvider $entityProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->entityClassResolver  = $entityClassResolver;
        $this->doctrine             = $doctrine;
        $this->entityProvider       = $entityProvider;
    }

    /**
     * Returns fields for the given entity
     *
     * @param string $entityName        Entity name. Can be full class name or short form: Bundle:Entity.
     * @param bool   $withRelations     Indicates whether fields of related entities should be returned as well.
     * @param bool   $withEntityDetails Indicates whether details of related entity should be returned as well.
     * @return array of fields sorted by field label (relations follows fields)
     *                                  .       'name'          - field name
     *                                  .       'type'          - field type
     *                                  .       'label'         - field label
     *                                  If a field represents a relation and $withRelations = true
     *                                  the following attributes are added:
     *                                  .       'relation_type'       - relation type
     *                                  .       'related_entity_name' - entity full class name
     *                                  If a field represents a relation and $withEntityDetails = true
     *                                  the following attributes are added:
     *                                  .       'related_entity_label'        - entity label
     *                                  .       'related_entity_plural_label' - entity plural label
     *                                  .       'related_entity_icon'         - an icon associated with an entity
     * @throws InvalidEntityException
     */
    public function getFields($entityName, $withRelations = false, $withEntityDetails = false)
    {
        $result    = array();
        $className = $this->entityClassResolver->getEntityClass($entityName);
        $em        = $this->getManagerForClass($className);
        $this->addFields($result, $className, $em);
        if ($withRelations) {
            $this->addRelations($result, $className, $em, $withEntityDetails);
        }
        $this->sortFields($result);

        return $result;
    }

    /**
     * Adds entity fields to $result
     *
     * @param array         $result
     * @param string        $className
     * @param EntityManager $em
     */
    protected function addFields(array &$result, $className, EntityManager $em)
    {
        // only configurable entities are supported
        if ($this->entityConfigProvider->hasConfig($className)) {
            $metadata = $em->getClassMetadata($className);
            foreach ($metadata->getFieldNames() as $fieldName) {
                $this->addField(
                    $result,
                    $fieldName,
                    $metadata->getTypeOfField($fieldName),
                    $this->getFieldLabel($className, $fieldName)
                );
            }
        }
    }

    /**
     * Adds a field to $result
     *
     * @param array  $result
     * @param string $name
     * @param string $type
     * @param string $label
     */
    protected function addField(array &$result, $name, $type, $label)
    {
        $result[] = array(
            'name'  => $name,
            'type'  => $type,
            'label' => $label
        );
    }

    /**
     * Adds entity relations to $result
     *
     * @param array         $result
     * @param string        $className
     * @param EntityManager $em
     * @param bool          $withEntityDetails
     */
    protected function addRelations(array &$result, $className, EntityManager $em, $withEntityDetails)
    {
        // only configurable entities are supported
        if ($this->entityConfigProvider->hasConfig($className)) {
            $metadata = $em->getClassMetadata($className);
            foreach ($metadata->getAssociationNames() as $associationName) {
                $targetClassName = $metadata->getAssociationTargetClass($associationName);
                if ($this->entityConfigProvider->hasConfig($targetClassName)) {
                    $targetFieldName = $metadata->getAssociationMappedByTargetField($associationName);
                    $targetMetadata  = $em->getClassMetadata($targetClassName);
                    $this->addRelation(
                        $result,
                        $associationName,
                        $targetMetadata->getTypeOfField($targetFieldName),
                        $this->getFieldLabel($className, $associationName),
                        $this->getRelationType($className, $associationName),
                        $targetClassName,
                        $withEntityDetails
                    );
                }
            }
        }
    }

    /**
     * Adds a relation to $result
     *
     * @param array  $result
     * @param string $name
     * @param string $type
     * @param string $label
     * @param string $relationType
     * @param string $relatedEntityName
     * @param bool   $withEntityDetails
     */
    protected function addRelation(
        array &$result,
        $name,
        $type,
        $label,
        $relationType,
        $relatedEntityName,
        $withEntityDetails
    ) {
        $relation = array(
            'name'                => $name,
            'type'                => $type,
            'label'               => $label,
            'relation_type'       => $relationType,
            'related_entity_name' => $relatedEntityName
        );
        if ($withEntityDetails) {
            $entity = $this->entityProvider->getEntity($relatedEntityName);
            foreach ($entity as $key => $val) {
                if (!in_array($key, ['name'])) {
                    $relation['related_entity_' . $key] = $val;
                }
            }
        }

        $result[] = $relation;
    }

    /**
     * Gets doctrine entity manager for the given class
     *
     * @param string $className
     * @return EntityManager
     * @throws InvalidEntityException
     */
    protected function getManagerForClass($className)
    {
        $manager = null;
        try {
            $manager = $this->doctrine->getManagerForClass($className);
        } catch (\ReflectionException $ex) {
            // ignore not found exception
        }
        if (!$manager) {
            throw new InvalidEntityException(sprintf('The "%s" entity was not found.', $className));
        }

        return $manager;
    }

    /**
     * Gets a field label
     *
     * @param string $className
     * @param string $fieldName
     * @return string
     */
    protected function getFieldLabel($className, $fieldName)
    {
        if ($this->entityConfigProvider->hasConfig($className, $fieldName)) {
            return $this->entityConfigProvider->getConfig($className, $fieldName)->get('label');
        }

        return $fieldName;
    }

    /**
     * Gets a relation type
     *
     * @param string $className
     * @param string $fieldName
     * @return string
     */
    protected function getRelationType($className, $fieldName)
    {
        if ($this->entityConfigProvider->hasConfig($className, $fieldName)) {
            return $this->entityConfigProvider->getConfig($className, $fieldName)->getId()->getFieldType();
        }

        return '';
    }

    /**
     * Sorts fields by its label (relations follows fields)
     *
     * @param array $fields
     */
    protected function sortFields(array &$fields)
    {
        usort(
            $fields,
            function ($a, $b) {
                if (isset($a['related_entity_name']) !== isset($b['related_entity_name'])) {
                    if (isset($a['related_entity_name'])) {
                        return 1;
                    }
                    if (isset($b['related_entity_name'])) {
                        return -1;
                    }
                }

                return strcasecmp($a['label'], $b['label']);
            }
        );
    }
}
