<?php

namespace App\Type;

use App\Helper\TypeMappingHelper;
use App\Type\Base\TypeBase;

class EnumerationType implements TypeBase
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
     * @var array
     */
    private $members = [];

    public function parseProtocol(array $object): void
    {
        // Sort members by value
        asort($object['members']);

        $this->name = $object['name'];
        $this->type = TypeMappingHelper::getBasicType($object['entries_type']);
        $this->members = $object['members'];
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
     * Get the value of members
     *
     * Auto generation by VSCode
     *
     * @return  array
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * Set the value of members
     *
     * Auto generation by VSCode
     *
     * @param   array  $members  
     *
     * @return  self
     */
    public function setMembers(array $members): self
    {
        $this->members = $members;

        return $this;
    }
}