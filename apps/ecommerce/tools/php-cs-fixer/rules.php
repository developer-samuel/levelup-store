<?php

declare(strict_types=1);

return [
    '@PSR12' => false,
    'blank_line_after_opening_tag' => false,
    'blank_line_after_namespace' => false,
    'single_line_empty_body' => true,
    'no_extra_blank_lines' => [
        'tokens' => ['throw', 'curly_brace_block', 'square_brace_block', 'parenthesis_brace_block'],
    ],
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => [
        'operators' => ['=>' => 'align_single_space'],
    ],

    'class_attributes_separation' => [
        'elements' => [
            'const' => 'none',
        ],
    ],

    'no_unused_imports' => true,
    'ordered_imports' => false,

    'trailing_comma_in_multiline' => [
        'elements' => ['arrays', 'arguments', 'parameters', 'match'],
    ],

    'phpdoc_separation' => true,
    'phpdoc_align' => false,
    'phpdoc_summary' => false,
    'phpdoc_order' => false,
    'phpdoc_indent' => false,
    'phpdoc_no_empty_return' => false,
    'phpdoc_no_package' => false,
    'phpdoc_scalar' => false,
    'phpdoc_trim' => false,
    'phpdoc_types_order' => false,
    'phpdoc_var_without_name' => false,
];
