<?php
$baseDir = dirname(__DIR__);
echo <<<COMMENT
/*
|--------------------------------------------------------------------------
| Poppy Framework cs Fixer
|--------------------------------------------------------------------------
| Document Url : https://github.com/FriendsOfPHP/PHP-CS-Fixer
| Current Dir  : {$baseDir}
*/\n
COMMENT;
if (strpos($baseDir, 'vendor/poppy') !== false) {
    $baseDir = dirname(dirname(dirname(__DIR__)));
    $folders = [
        $baseDir.'/config',
        $baseDir.'/modules',
    ];
} else {
    // for development
    $folders = [
        $baseDir.'/config',
        $baseDir.'/framework',
        $baseDir.'/extensions',
        $baseDir.'/modules',
    ];
}

// 参考 Symfony\Component\Finder\Finder
$finder = PhpCsFixer\Finder::create()
    ->exclude('database')
    ->in($folders);
return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setCacheFile($baseDir . '/storage/app/.php_cs.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space',
                '.=' => 'align_single_space',
                '/=' => 'align_single_space',
                '+=' => 'align_single_space',
                '*=' => 'align_single_space',
                '-=' => 'align_single_space',
                '&=' => 'align_single_space',
                '='  => 'align_single_space',
            ],
        ],
        'blank_line_before_return' => true,
        'braces' => false,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'dir_constant' => true,
        'elseif' => true,
        'encoding' => true,
        'ereg_to_preg' => true,
        'function_typehint_space' => true,
        'function_to_constant' => true,
        'hash_to_slash_comment' => true,
        'indentation_type' => true,
        'linebreak_after_opening_tag' => false,
        'lowercase_cast' => true,
        'class_attributes_separation' => [
            'elements' => ['method', 'property'],
        ],
        'native_function_casing' => true,
        'new_with_braces' => true,
        'no_alias_functions' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_blank_lines_before_namespace' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'break',
            'continue',
            'extra',
            // 'return',
            'throw',
            'use',
            'parenthesis_brace_block',
            // 'square_brace_block',
            'curly_brace_block'
        ],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => [
            'break',
            'clone',
            'continue',
            'echo_print',
            'return',
            'switch_case',
        ],
        'no_unreachable_default_argument_value' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => false,
        'normalize_index_brace' => true,
        'ordered_imports' => true,
        'phpdoc_indent' => true,
        'phpdoc_scalar' => [
            'types' => [
                'boolean', 'double', 'integer', 'str'
            ],
        ],
        'phpdoc_types' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_annotation_without_dot' => true,
        'self_accessor' => true,
        'short_scalar_cast' => true,
        'single_blank_line_at_eof' => false,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setIndent("\t")        // 设置缩进为制表符
    ->setLineEnding("\n")    // 文件已 \n 结尾
    ->setFinder($finder);