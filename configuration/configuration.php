<?php

use App\Hook\GenerationHook;
use App\Type\ClassType;

use Twig\{
    TwigFilter,
    TwigSimpleFilter,
};

/**
 * Output of the generated protocol
 */
const OUTPUT_FOLDER = __DIR__ . '/../output';

/**
 * Extension of generated protocol files
 */
const LANGUAGE_EXTENSION = '.cs';

/**
 * Basic AS types override
 */
const BASIC_TYPE_MAPPER = [
    'uint' => 'uint',
    'int' => 'int',
    'Boolean' => 'bool',
    'String' => 'string',
    'Number' => 'long'
];

/**
 * Field type mapped by writing method(s) 
 */
const WRITE_TYPE_MAPPER = [
    'byte' => [ 'writeByte' ],
    'long' => [ 'writeVarLong' ],
    'uint' => [ 'writeVarUInt', 'writeUInt' ],
    'int' => [ 'writeInt', 'writeVarInt' ],
    'short' => [ 'writeShort', 'writeVarShort' ],
    'ushort' => [ 'writeUShort', 'writeVarUShort' ],
    'string' => [ 'writeUTF' ],
    'bool' => [ 'writeBoolean' ],
    'float' => [ 'writeFloat' ],
    'char' => [ 'writeChar' ],
    'double' => [ 'writeDouble' ]
];

/**
 * Original writing method override
 */
const WRITE_METHOD_OVERRIDE = [
    'writeUnsignedInt' => 'writeUInt',
];

/**
 * Reading method associated by the corresponding writing method
 */
const READ_METHOD_MAPPER = [
    'writeByte' => 'readByte',
    'writeVarLong' => 'readVarLong',
    'writeVarUInt' => 'readVarUInt',
    'writeVarInt' => 'readVarInt',
    'writeVarShort' => 'readVarShort',
    'writeVarUShort' => 'readVarUShort',
    'writeUShort' => 'readUShort',
    'writeShort' => 'readShort',
    'writeInt' => 'readInt',
    'writeUInt' => 'readUInt',
    'writeUTF' => 'readUTF',
    'writeBoolean' => 'readBoolean',
    'writeFloat' => 'readFloat',
    'writeChar' => 'readChar',
    'writeDouble' => 'readDouble'
];

/**
 * Configuration for the my custom filter (made for my project only, you can remove it)
 */
const RESERVED_WORD_CLASS_MEMBER_STRATEGY = [
    'prefix' => '',
    'suffix' => '_',
    'list' => [
        'object',
        'id',
        'class',
        'params',
        'base',
        'messageid',
        'typeid'
    ]
];

/**
 * Generation initialisation
 * 
 * DO NOT TOUCH THIS
 */
GenerationHook::initializeTwig();

/**
 * Add "ident" filter to indent correctly with $n spaces
 * 
 * @param string $string    The string to place
 * @param int $number       Number of desired spaces
 */
GenerationHook::getTwig()->addFilter(
    new TwigFilter('ident', function (string $string, int $number): string {
        $spaces = str_repeat(' ', $number);
    
        return rtrim(preg_replace('#^(.+)$#m', sprintf('%1$s$1', $spaces), $string), '\t ');
    }, array('is_safe' => array('all')))
);

/**
 * Add "ucfirst" filter (same behavior as native PHP "ucfirst")
 */
GenerationHook::getTwig()->addFilter(new TwigFilter('ucfirst', 'ucfirst'));

/**
 * Add custom "tolower" method for constructor members
 */
GenerationHook::getTwig()->addFilter(
    new TwigFilter('mytolower', function (string $string): string {
        $toLowerCount = 0;

        foreach (str_split($string) as $char) {
            if (ctype_upper($char)) {
                $toLowerCount++;
            } else {
                break;
            }
        }

        $toLower = substr($string, 0, $toLowerCount);
    
        return strtolower($toLower) . substr($string, $toLowerCount);
    }, array('is_safe' => array('all')))
);

/**
 * Custom filter to avoid using of C# reserved words or types having 
 * the same name as class members
 */
GenerationHook::addFilter(function (object $object, string $_): bool {
    if ($object instanceof ClassType === true) {
        foreach ($object->getFields() as $field) {
            if (strtoupper($field->getName()) === strtoupper($field->getType()) ||
                in_array(strtoupper($field->getName()), array_map(function (string $word): string {
                    return strtoupper($word);
                }, RESERVED_WORD_CLASS_MEMBER_STRATEGY['list']))) {
                $field->setName(RESERVED_WORD_CLASS_MEMBER_STRATEGY['prefix'] . $field->getName() . RESERVED_WORD_CLASS_MEMBER_STRATEGY['suffix']);
            }
        }
    }

    return true;
});