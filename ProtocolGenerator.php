<?php

const ROOT = __DIR__;

require_once __DIR__ . '/vendor/autoload.php';

use App\Hook\GenerationHook;
use App\Type\{
    ClassType,
    ClassFieldType,
    EnumerationType,
};

/**
 * Path parsing and generation functions
 */
function createOutputFolder(string $folderPath) {
    @mkdir($folderPath, 0777, true);
}

function getOutputFolder(string $namespace): string {
    return str_replace('.', '/', str_replace('com.ankamagames.dofus.network.', '', $namespace)) . '/';
}

function getOutputPath(string $namespace, string $fileName): string {
    $folderPath = OUTPUT_FOLDER . '/' . getOutputFolder($namespace);

    createOutputFolder($folderPath);

    return $folderPath . '/' . $fileName . LANGUAGE_EXTENSION;
}

if (($rawProtocolContent = file_get_contents($argv[1] ?? 'protocol.json')) === null) {
    echo 'Unable to read protocol file.' . PHP_EOL;

    return 1;
}

if (($rawProtocol = json_decode($rawProtocolContent, true)) === false) {
    echo 'Unable to parse protocol file:' . PHP_EOL . json_last_error_msg() . PHP_EOL;

    return 1;
}

$protocol = [
    'enumerations' => [],
    'types' => [],
    'messages' => []
];

/**
 * Registering
 */
foreach ($rawProtocol['enumerations'] as $object) {
    $enumerationType = new EnumerationType();
    $enumerationType->parseProtocol($object);

    $protocol['enumerations'][$enumerationType->getName()] = $enumerationType;
}

foreach ($rawProtocol['messages'] as $object) {
    $messageType = new ClassType();
    $messageType->parseProtocol($object);
    $messageType->setForceOverride(true);

    $protocol['messages'][$messageType->getName()] = $messageType;
}

foreach ($rawProtocol['types'] as $object) {
    $typeType = new ClassType();
    $typeType->parseProtocol($object);

    $protocol['types'][$typeType->getName()] = $typeType;
}

/**
 * Post registration dependencies
 */
foreach (array_merge(array_values($protocol['types']), array_values($protocol['messages'])) as $classType) {
    $classType->setDependencies($protocol);
}

foreach ($protocol as $group) {
    GenerationHook::executeFilters($group);
}

/**
 * Export
 */
$twig = GenerationHook::getTwig();

foreach ($protocol['enumerations'] as $enumeration) {
    file_put_contents(
        getOutputPath('enumerations', $enumeration->getName()), 
        $twig->render('enum.twig', ['class' => $enumeration])
    );
}

foreach ($protocol['messages'] as $message) {
    file_put_contents(
        getOutputPath($message->getNamespace(), $message->getName()), 
        $twig->render('message.twig', ['class' => $message])
    );
}

foreach ($protocol['types'] as $type) {
    file_put_contents(
        getOutputPath($type->getNamespace(), $type->getName()), 
        $twig->render('type.twig', ['class' => $type])
    );
}