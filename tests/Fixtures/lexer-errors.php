<?php

return [
    'missing-variable-terminator' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => 'Variable was not properly terminated with: }}',
        'lineNumber' => 2,
    ],
    'missing-tag-terminator' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => 'Tag was not properly terminated with: %}',
        'lineNumber' => 2,
    ],
    'unexpected-character' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => 'Unexpected character %',
        'lineNumber' => 3,
    ],
    'comment-tag-never-closed' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => '\'comment\' tag was never closed',
        'lineNumber' => 2,
    ],
    'raw-tag-never-closed' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => '\'raw\' tag was never closed',
        'lineNumber' => 2,
    ],
    'unexpected-end-of-template' => [
        'class' => 'Keepsuit\\Liquid\\Exceptions\\SyntaxException',
        'message' => 'Unexpected end of template',
        'lineNumber' => 1,
    ],
];
