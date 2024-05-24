<?php

const ROOT = __DIR__;

require_once __DIR__ . '/vendor/autoload.php';

use App\Helper\GenerationHelper;
use App\Hook\GenerationHook;
use App\Type\{
    ClassType,
    EnumerationType,
};

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

foreach ($protocol as $groupName => $group) {
    GenerationHook::executeFilters($group, $groupName);
}

/**
 * Export
 */
$twig = GenerationHook::getTwig();

file_put_contents(GenerationHelper::getOutputPath('', 'protocol'),
    $twig->render('protocol.twig', [
        'enumerations' => array_map(function (EnumerationType $enumeration) use ($twig): string {
            return $twig->render('enum.twig', ['class' => $enumeration]);
        }, $protocol['enumerations']),
        'types' => array_map(function (ClassType $type) use ($twig): string {
            return $twig->render('type.twig', ['class' => $type]);
        }, $protocol['types']),
        'messages' => array_map(function (ClassType $message) use ($twig): string {
            return $twig->render('message.twig', ['class' => $message]);
        }, $protocol['messages']),
        'typesObjects' => $protocol['types'],
        'messagesObjects' => $protocol['messages'],
    ]),
);

/*
foreach ($protocol['enumerations'] as $enumeration) {
    file_put_contents(
        GenerationHelper::getOutputPath('enumerations', $enumeration->getName()),
        $twig->render('enum.twig', ['class' => $enumeration])
    );
}

foreach ($protocol['messages'] as $message) {
    file_put_contents(
        GenerationHelper::getOutputPath($message->getNamespace(), $message->getName()), 
        $twig->render('message.twig', ['class' => $message])
    );
}

foreach ($protocol['types'] as $type) {
    file_put_contents(
        GenerationHelper::getOutputPath($type->getNamespace(), $type->getName()), 
        $twig->render('type.twig', ['class' => $type])
    );
}
*/