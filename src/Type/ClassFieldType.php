<?php

namespace App\Type;

use App\Helper\{
    MethodMappingHelper,
    TypeMappingHelper
};
use App\Type\Base\TypeBase;

class ClassFieldType implements TypeBase
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $lengthType;

    /**
     * @var bool
     */
    private $isVector = false;

    /**
     * @var bool
     */
    private $isObjectType = false;

    /**
     * @var ?string
     */
    private $value = null;

    /**
     * @var array
     */
    private $bounds;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var int
     */
    private $constantLength = 0;

    /**
     * @var string
     */
    private $writeMethod;

    /**
     * @var string
     */
    private $readMethod;

    /**
     * @var string
     */
    private $lengthWriteMethod;

    /**
     * @var string
     */
    private $lengthReadMethod;

    /**
     * @var string
     */
    private $typeIdWriteMethod;

    /**
     * @var string
     */
    private $typeIdReadMethod;

    /**
     * @var bool
     */
    private $useBooleanByteWrapper = false;

    /**
     * @var int
     */
    private $booleanByteWrapperPosition = 0;

    /**
     * @var bool
     */
    private $needTypeIdDefinition = false;

    public function parseProtocol(array $object): void
    {
        $this->name = $object['name'];
        $this->isVector = $object['is_vector'] ?? false;
        $this->constantLength = $object['constant_length'] ?? 0;
        $this->value = $object['default_value'] ?? null;
        $this->position = $object['position'] ?? 0;
        $this->useBooleanByteWrapper = $object['use_boolean_byte_wrapper'] ?? false;
        $this->booleanByteWrapperPosition = $object['boolean_byte_wrapper_position'] ?? 0;
        $this->writeMethod = MethodMappingHelper::getMethod('write_method', $object);
        $this->readMethod = MethodMappingHelper::getReadingMethod($this->writeMethod);
        $this->bounds = isset($object['bounds']) ? [
            'min' => $object['bounds']['low'] ?? null,
            'max' => $object['boundes']['up'] ?? null
        ] : [
            'min' => null, 
            'max' => null
        ];

        if ($this->isVector === true) {
            $this->lengthWriteMethod = MethodMappingHelper::getMethod('write_length_method', $object);
            $this->lengthReadMethod = MethodMappingHelper::getReadingMethod($this->lengthWriteMethod);

            $typeWritingOutput = TypeMappingHelper::getTypeByWritingMethod($this->lengthWriteMethod, 'int');
            
            $this->lengthType = $typeWritingOutput['type'];
        }

        $typeWritingOutput = TypeMappingHelper::getTypeByWritingMethod($this->writeMethod, $object['type']);

        $this->type = $typeWritingOutput['type'];
        
        if (($this->isObjectType = $typeWritingOutput['isObjectType']) === true) {
            $this->needTypeIdDefinition = $object['prefixed_by_type_id'] ?? false;
            $this->typeIdWriteMethod = $object['write_type_id_method'] ?? null;
            $this->typeIdReadMethod = MethodMappingHelper::getReadingMethod($this->typeIdWriteMethod);
        }
    }

    /**
     * Get the value of name
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * Auto generation by VSCode
     *
     * @param   string  $name  
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of type
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * Auto generation by VSCode
     *
     * @param   string  $type  
     *
     * @return  self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of lengthType
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getLengthType(): string
    {
        return $this->lengthType;
    }

    /**
     * Set the value of lengthType
     *
     * Auto generation by VSCode
     *
     * @param   string  $lengthType  
     *
     * @return  self
     */
    public function setLengthType(string $lengthType): self
    {
        $this->lengthType = $lengthType;

        return $this;
    }

    /**
     * Get the value of isVector
     *
     * Auto generation by VSCode
     *
     * @return  bool
     */
    public function getIsVector(): bool
    {
        return $this->isVector;
    }

    /**
     * Set the value of isVector
     *
     * Auto generation by VSCode
     *
     * @param   bool  $isVector  
     *
     * @return  self
     */
    public function setIsVector(bool $isVector): self
    {
        $this->isVector = $isVector;

        return $this;
    }

    /**
     * Get the value of isObjectType
     *
     * Auto generation by VSCode
     *
     * @return  bool
     */
    public function getIsObjectType(): bool
    {
        return $this->isObjectType;
    }

    /**
     * Set the value of isObjectType
     *
     * Auto generation by VSCode
     *
     * @param   bool  $isObjectType  
     *
     * @return  self
     */
    public function setIsObjectType(bool $isObjectType): self
    {
        $this->isObjectType = $isObjectType;

        return $this;
    }

    /**
     * Get the value of value
     *
     * Auto generation by VSCode
     *
     * @return  ?string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * Auto generation by VSCode
     *
     * @param   ?string  $value  
     *
     * @return  self
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of bounds
     *
     * Auto generation by VSCode
     *
     * @return  array
     */
    public function getBounds(): array
    {
        return $this->bounds;
    }

    /**
     * Set the value of bounds
     *
     * Auto generation by VSCode
     *
     * @param   array  $bounds  
     *
     * @return  self
     */
    public function setBounds(array $bounds): self
    {
        $this->bounds = $bounds;

        return $this;
    }

    /**
     * Get the value of position
     *
     * Auto generation by VSCode
     *
     * @return  int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * Auto generation by VSCode
     *
     * @param   int  $position  
     *
     * @return  self
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of constantLength
     *
     * Auto generation by VSCode
     *
     * @return  int
     */
    public function getConstantLength(): int
    {
        return $this->constantLength;
    }

    /**
     * Set the value of constantLength
     *
     * Auto generation by VSCode
     *
     * @param   int  $constantLength  
     *
     * @return  self
     */
    public function setConstantLength(int $constantLength): self
    {
        $this->constantLength = $constantLength;

        return $this;
    }

    /**
     * Get the value of writeMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getWriteMethod(): ?string
    {
        return $this->writeMethod;
    }

    /**
     * Set the value of writeMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $writeMethod  
     *
     * @return  self
     */
    public function setWriteMethod(?string $writeMethod): self
    {
        $this->writeMethod = $writeMethod;

        return $this;
    }

    /**
     * Get the value of readMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getReadMethod(): ?string
    {
        return $this->readMethod;
    }

    /**
     * Set the value of readMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $readMethod  
     *
     * @return  self
     */
    public function setReadMethod(?string $readMethod): self
    {
        $this->readMethod = $readMethod;

        return $this;
    }

    /**
     * Get the value of lengthWriteMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getLengthWriteMethod(): ?string
    {
        return $this->lengthWriteMethod;
    }

    /**
     * Set the value of lengthWriteMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $lengthWriteMethod  
     *
     * @return  self
     */
    public function setLengthWriteMethod(?string $lengthWriteMethod): self
    {
        $this->lengthWriteMethod = $lengthWriteMethod;

        return $this;
    }

    /**
     * Get the value of lengthReadMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getLengthReadMethod(): ?string
    {
        return $this->lengthReadMethod;
    }

    /**
     * Set the value of lengthReadMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $lengthReadMethod  
     *
     * @return  self
     */
    public function setLengthReadMethod(?string $lengthReadMethod): self
    {
        $this->lengthReadMethod = $lengthReadMethod;

        return $this;
    }

    /**
     * Get the value of typeIdWriteMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getTypeIdWriteMethod(): string
    {
        return $this->typeIdWriteMethod;
    }

    /**
     * Set the value of typeIdWriteMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $typeIdWriteMethod  
     *
     * @return  self
     */
    public function setTypeIdWriteMethod(string $typeIdWriteMethod): self
    {
        $this->typeIdWriteMethod = $typeIdWriteMethod;

        return $this;
    }

    /**
     * Get the value of typeIdReadMethod
     *
     * Auto generation by VSCode
     *
     * @return  string
     */
    public function getTypeIdReadMethod(): string
    {
        return $this->typeIdReadMethod;
    }

    /**
     * Set the value of typeIdReadMethod
     *
     * Auto generation by VSCode
     *
     * @param   string  $typeIdReadMethod  
     *
     * @return  self
     */
    public function setTypeIdReadMethod(string $typeIdReadMethod): self
    {
        $this->typeIdReadMethod = $typeIdReadMethod;

        return $this;
    }

    /**
     * Get the value of useBooleanByteWrapper
     *
     * Auto generation by VSCode
     *
     * @return  bool
     */
    public function getUseBooleanByteWrapper(): bool
    {
        return $this->useBooleanByteWrapper;
    }

    /**
     * Set the value of useBooleanByteWrapper
     *
     * Auto generation by VSCode
     *
     * @param   bool  $useBooleanByteWrapper  
     *
     * @return  self
     */
    public function setUseBooleanByteWrapper(bool $useBooleanByteWrapper): self
    {
        $this->useBooleanByteWrapper = $useBooleanByteWrapper;

        return $this;
    }

    /**
     * Get the value of booleanByteWrapperPosition
     *
     * Auto generation by VSCode
     *
     * @return  int
     */
    public function getBooleanByteWrapperPosition(): int
    {
        return $this->booleanByteWrapperPosition;
    }

    /**
     * Set the value of booleanByteWrapperPosition
     *
     * Auto generation by VSCode
     *
     * @param   int  $booleanByteWrapperPosition  
     *
     * @return  self
     */
    public function setBooleanByteWrapperPosition(int $booleanByteWrapperPosition): self
    {
        $this->booleanByteWrapperPosition = $booleanByteWrapperPosition;

        return $this;
    }

    /**
     * Get the value of needTypeIdDefinition
     *
     * Auto generation by VSCode
     *
     * @return  bool
     */
    public function getNeedTypeIdDefinition(): bool
    {
        return $this->needTypeIdDefinition;
    }

    /**
     * Set the value of needTypeIdDefinition
     *
     * Auto generation by VSCode
     *
     * @param   bool  $needTypeIdDefinition  
     *
     * @return  self
     */
    public function setNeedTypeIdDefinition(bool $needTypeIdDefinition): self
    {
        $this->needTypeIdDefinition = $needTypeIdDefinition;

        return $this;
    }
}