<?php


function snakeToCamelCase(string $string): string {
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
}

function camelCaseToSnake(string $string): string {
    return strtolower(preg_replace('/[A-Z]/', '_$0', $string));
}

function snakeToKebabCase(string $string): string {
    return strtolower(str_replace('_', '-', $string));
}

function kebabToSnakeCase(string $string): string {
    return str_replace('-', '_', $string);
}

function snakeToPascalCase(string $string): string {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
}

function pascalToSnakeCase(string $string): string {
    return strtolower(preg_replace('/[A-Z]/', '_$0', $string));
}
