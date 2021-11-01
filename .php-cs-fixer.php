<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/')
    ->exclude('Resources/config')
    ->exclude('src/DependencyInjection')
    ->exclude('test_e2e')
;

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true)
    ->setRules(
        [
            '@DoctrineAnnotation' => true,
            '@Symfony' => true,
            '@Symfony:risky' => true,

            // @Symfony code styles rules blacklisting:
            'method_chaining_indentation' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_trailing_comma_in_list_call' => false,
            'php_unit_fqcn_annotation' => false,
            'phpdoc_align' => false,
            'phpdoc_annotation_without_dot' => false,
            'phpdoc_indent' => false,
            'phpdoc_inline_tag_normalizer' => false,
            'phpdoc_no_access' => false,
            'phpdoc_no_alias_tag' => false,
            'phpdoc_no_empty_return' => false,
            'phpdoc_no_package' => false,
            'phpdoc_no_useless_inheritdoc' => false,
            'phpdoc_return_self_reference' => false,
            'phpdoc_scalar' => false,
            'phpdoc_separation' => false,
            'phpdoc_single_line_var_spacing' => false,
            'phpdoc_summary' => false,
            'phpdoc_to_comment' => false,
            'phpdoc_trim' => false,
            'phpdoc_types' => false,
            'phpdoc_var_without_name' => false,
            'error_suppression' => false,
            'standardize_not_equals' => false,

            // @Symfony customised rules
            'concat_space' => ['spacing' => 'one'],
            'native_function_invocation' => false,
            'single_quote' => ['strings_containing_single_quote_chars' => true],
            'visibility_required' => ['elements' => ['property', 'method', 'const']],
            'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],

            // Additional code style rules whitelisting:
            'align_multiline_comment' => true,
            'array_indentation' => true,
            'array_syntax' => ['syntax' => 'short'],
            'combine_consecutive_issets' => true,
            'declare_strict_types' => true,
            'explicit_indirect_variable' => true,
            'explicit_string_variable' => true,
            'fully_qualified_strict_types' => true,
            'linebreak_after_opening_tag' => true,
            'list_syntax' => ['syntax' => 'short'],
            'mb_str_functions' => true,
            'multiline_comment_opening_closing' => true,
            'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
            'no_alternative_syntax' => true,
            'no_superfluous_elseif' => true,
            'ordered_imports' => true,
            'ordered_interfaces' => true,
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(false)
;

return $config;
