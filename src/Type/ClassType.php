<?php

namespace App\Type;

use App\Type\Base\TypeBase;

class ClassType implements TypeBase
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $parentName;

    /**
     * @var ClassType
     */
    private $parent = null;

    /**
     * @var array
     */
    private $children = [];
    
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var bool
     */
    private $hasVectorDependency = false;

    /**
     * @var bool
     */
    private $hasTypeDependency = false;

    /**
     * @var bool
     */
    private $hasEnumerationDependency = false;

    /**
     * @var array
     */
    private $typeDependencies = [];

    /**
     * @var array
     */
    private $enumerationDependencies = [];

    /**
     * @var bool
     */
    private $serializeParent = false;

    /**
     * @var bool
     */
    private $forceOverride = false;

    public function parseProtocol(array $object): void
    {
        // Sort fields by position
        usort($object['fields'], function (array $a, array $b) {
            if ($a['position'] === $b['position']) {
                return $a['boolean_byte_wrapper_position'] <=> $b['boolean_byte_wrapper_position'];
            }

            return $a['position'] <=> $b['position'];
        });

        $this->id = $object['protocolID'];
        $this->name = $object['name'];
        $this->parentName = $object['super'] ?? null;
        $this->serializeParent = $object['super_serialize'] ?? false;
        $this->namespace = $object['namespace'];

        foreach ($object['fields'] as $objectField) {
            $field = new ClassFieldType();
            $field->parseProtocol($objectField);

            if ($field->getIsVector()) {
                $this->hasVectorDependency = true;
            }

            $this->fields[] = $field;
        }
    }

    /**
     * Set type dependencies
     */
    public function setDependencies(array $protocol): void
    {
        foreach ($protocol as $groupName => $group) {
            if ($this->parentName !== null && isset($group[$this->parentName])) {
                // Set current type parent
                $this->parent = $group[$this->parentName];

                // Add current type to parent children
                $this->parent->addChild($this);
            }
    
            $groupDependencies = [];
    
            foreach ($this->fields as $field) {
                if ($field->getIsObjectType() === true && isset($group[$field->getType()])) {
                    $groupDependencies[] = $group[$field->getType()];
                }
            }
    
            if (count($groupDependencies) > 0) {
                switch ($groupName) {
                    case 'types':
                        $this->hasTypeDependency = true;
                        $this->typeDependencies = $groupDependencies;
    
                        break;
                    case 'enumerations':
                        $this->hasEnumerationDependency = true;
                        $this->enumerationDependencies = $groupDependencies;
    
                        break;
                }
            }
        }
    }

    private function resetByteWrapperPositions()
    {
        $isInWrapper = false;
        $lastWrapperIndex = 0;
        $lastWrapperPosition = 0;

        foreach ($this->fields as $field) {
            if ($field->getUseBooleanByteWrapper() === true) {
                if ($isInWrapper === false) {
                    $isInWrapper = true;
                } elseif ($lastWrapperPosition !== $field->getPosition()) {
                    $lastWrapperIndex = 0;
                    $lastWrapperPosition = $field->getPosition();
                }

                $field->setBooleanByteWrapperPosition($lastWrapperIndex);

                $lastWrapperIndex++;
            } else {
                $isInWrapper = false;
                $lastWrapperIndex = 0;
            }
        }
    }

    public function isVirtual(): bool 
    {
        return $this->parent === null && count($this->children) > 0;
    }

    public function isOverriding(): bool 
    {
        return $this->parent !== null || $this->forceOverride;
    }

    public function getParentFields($asParent = false): array
    {
        $fields = [];

        if ($asParent) {
            $fields = $this->fields;
        }

        return array_merge($this->getParent() ?
            $this->getParent()->getParentFields(true) + $this->getParent()->fields :
            []
        , $fields);
    }

    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */ 
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of parentName
     *
     * @return  string
     */ 
    public function getParentName(): ?string
    {
        return $this->parentName;
    }

    /**
     * Set the value of parentName
     *
     * @param  string  $parentName
     *
     * @return  self
     */ 
    public function setParentName(?string $parentName): self
    {
        $this->parentName = $parentName;

        return $this;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of parent
     *
     * @return  ClassType
     */ 
    public function getParent(): ?ClassType
    {
        return $this->parent;
    }

    /**
     * Set the value of parent
     *
     * @param  ClassType  $parent
     *
     * @return  self
     */ 
    public function setParent(?ClassType $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

   /**
     * Get the value of children
     *
     * @return  array
     */ 
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(ClassType $classType): self
    {
        if (in_array($classType, $this->children) === false) {
            $this->children[] = $classType;
        }

        return $this;
    }

    /**
     * Set the value of children
     *
     * @param  array  $children
     *
     * @return  self
     */ 
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get the value of namespace
     *
     * @return  string
     */ 
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Set the value of namespace
     *
     * @param  string  $namespace
     *
     * @return  self
     */ 
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get the value of fields
     *
     * @return  array
     */ 
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Add a field
     */
    public function addField(ClassFieldType $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Set the value of fields
     *
     * @param  array  $fields
     *
     * @return  self
     */ 
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get the value of hasVectorDependency
     *
     * @return  bool
     */ 
    public function getHasVectorDependency(): bool
    {
        return 
            $this->hasVectorDependency ||
            $this->parent && $this->parent->getHasVectorDependency()
        ;
    }

    /**
     * Set the value of hasVectorDependency
     *
     * @param  bool  $hasVectorDependency
     *
     * @return  self
     */ 
    public function setHasVectorDependency(bool $hasVectorDependency): self
    {
        $this->hasVectorDependency = $hasVectorDependency;

        return $this;
    }

    /**
     * Get the value of hasTypeDependency
     *
     * @return  bool
     */ 
    public function getHasTypeDependency(): bool
    {
        return 
            $this->hasTypeDependency === true || 
            $this->parent && $this->parent->getHasTypeDependency()
        ;
    }

    /**
     * Set the value of hasTypeDependency
     *
     * @param  bool  $hasTypeDependency
     *
     * @return  self
     */ 
    public function setHasTypeDependency(bool $hasTypeDependency): self
    {
        $this->hasTypeDependency = $hasTypeDependency;

        return $this;
    }

    /**
     * Get the value of hasEnumerationDependency
     *
     * @return  bool
     */ 
    public function getHasEnumerationDependency()
    {
        return 
            $this->hasEnumerationDependency === true || 
            $this->parent && $this->parent->getHasEnumerationDependency()
        ;
    }

    /**
     * Set the value of hasEnumerationDependency
     *
     * @param  bool  $hasEnumerationDependency
     *
     * @return  self
     */ 
    public function setHasEnumerationDependency(bool $hasEnumerationDependency)
    {
        $this->hasEnumerationDependency = $hasEnumerationDependency;

        return $this;
    }

    /**
     * Get the value of typeDependencies
     *
     * @return  array
     */ 
    public function getTypeDependencies(): array
    {
        $dependencies = $this->typeDependencies;

        if ($this->parent) {
            $dependencies += $this->parent->getTypeDependencies();
        }

        return $dependencies;
    }

    public function addTypeDependency(string $type): self
    {
        if (in_array($type, $this->typeDependencies) === false) {
            $this->typeDependencies[] = $type;
        }

        return $this;
    }

    /**
     * Set the value of typeDependencies
     *
     * @param  array  $typeDependencies
     *
     * @return  self
     */ 
    public function setTypeDependencies(array $typeDependencies): self
    {
        $this->typeDependencies = $typeDependencies;

        return $this;
    }

    /**
     * Get the value of enumerationDependencies
     *
     * @return  array
     */ 
    public function getEnumerationDependencies(): array
    {
        $dependencies = $this->enumerationDependencies;

        if ($this->parent) {
            $dependencies += $this->parent->getEnumerationDependencies();
        }

        return $dependencies;
    }

    public function addEnumerationDependency(string $type): self
    {
        if (in_array($type, $this->enumerationDependencies) === false) {
            $this->enumerationDependencies[] = $type;
        }

        return $this;
    }

    /**
     * Set the value of enumerationDependencies
     *
     * @param  array  $enumerationDependencies
     *
     * @return  self
     */ 
    public function setEnumerationDependencies(array $enumerationDependencies): self
    {
        $this->enumerationDependencies = $enumerationDependencies;

        return $this;
    }

    /**
     * Get the value of serializeParent
     *
     * @return  bool
     */ 
    public function getSerializeParent(): bool
    {
        return $this->serializeParent;
    }

    /**
     * Set the value of serializeParent
     *
     * @param  bool  $serializeParent
     *
     * @return  self
     */ 
    public function setSerializeParent(bool $serializeParent): self
    {
        $this->serializeParent = $serializeParent;

        return $this;
    }
 
    /**
     * Get the value of forceOverride
     *
     * @return  bool
     */ 
    public function getForceOverride(): bool
    {
        return $this->forceOverride;
    }

    /**
     * Set the value of forceOverride
     *
     * @param  bool  $forceOverride
     *
     * @return  self
     */ 
    public function setForceOverride(bool $forceOverride): self
    {
        $this->forceOverride = $forceOverride;

        return $this;
    }
}