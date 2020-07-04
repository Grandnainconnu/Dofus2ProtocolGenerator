# Dofus2ProtocolGenerator

## Requirements
- composer
- php >= 7.3

## Instalation
```
git clone ... folder
cd folder
composer install
```

## Usage
`php ProtocolGenerator.php protocolFilePath.json`

## Configuration
You have the `configuration.php` inside the configuration directory, you can change:
```
OUTPUT_FOLDER: containing the future generated protocol
LANGUAGE_EXTENSION: extension of each protocol file
BASIC_TYPE_MAPPER: to override AS basic types
WRITE_TYPE_MAPPER: to map each type depedending on the protocol write method
WRITE_METHOD_OVERRIDE: to override protocol write methods names (example: writeUnsignedInt -> writeUInt)
READ_METHOD_MAPPER: to map each reading method depending on the writing one
```

And also add filters before generation, defined like this:

Simple filter:
```
GenerationHook::addFilter(function (object $object): bool {
    // $object will be ClassType (for messages and types) or EnumerationType

    // Your code here...
    
    // Return false if you don't want the current object to be generated
    return true;
});
```

Global filter:
```
GenerationHook::addFilter(function (array &$objects): void {
    // $objects will be an array of ClassType or EnumerationType
    // It is also a reference so you can update the array $objects directly to affect the generation

    // Your code here...
}, true);
```

## Templates
You have defaults templates for C# in the templates folder, you can edit them if needed for ANY language.
Each template will have a `class` parameter given containing all necessary members.

### For enumeration template:
- class.name (string)
- class.type (string)
- class.members (array)

### For message and type template:
- class.name (string)
- class.type (string)
- parent (object or null)
- children (array)
- namespace (string)
- fields (array)
  - #### Field type
  - name (string)
  - type (string)
  - lengthType (string or null)
  - isVector (bool)
  - isObjectType (bool)
  - value (string or null)
  - bounds (array containing 'min' and 'max' indexes)
  - position (int)
  - constantLength (int)
  - writeMethod (string or null)
  - readMethod (string or null)
  - lengthWriteMethod (string or null)
  - lengthReadMethod (string or null)
  - typeIdWriteMethod (string or null)
  - typeIdReadMethod (string or null)
  - useBooleanByteWrapper (bool)
  - booleanByteWrapperPosition (int)
  - needTypeIdDefinition (bool)
- hasVectorDependency (bool)
- hasTypeDependency (bool)
- hasEnumerationDependency (bool)
- typeDependencies (array)
- enumerationDependencies (array)
- serializeParent (bool)
- forceOverride (bool)
- isVirtual (bool)
- isOverriding (bool)
