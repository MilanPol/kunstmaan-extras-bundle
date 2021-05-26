<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff;
use ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff;
use ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;

return [
    'preset' => 'symfony',
    'ide'    => 'phpstorm',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
        'bin',
        'src/Kernel.php',
        'src/Migrations',
    ],

    'remove' => [
        SpaceAfterNotSniff::class,
        DisallowLateStaticBindingForConstantsSniff::class,
        ForbiddenNormalClasses::class,
        ForbiddenSetterSniff::class,
        SuperfluousAbstractClassNamingSniff::class,
        SuperfluousInterfaceNamingSniff::class,
        SuperfluousExceptionNamingSniff::class,
        OrderedImportsFixer::class,
        OrderedClassElementsFixer::class,
        AlphabeticallySortedUsesSniff::class,
        DisallowShortTernaryOperatorSniff::class,
        DocCommentSpacingSniff::class,
        ParameterTypeHintSniff::class,
        DisallowMixedTypeHintSniff::class,
        FunctionDeclarationFixer::class,
        EmptyStatementSniff::class,
        UselessOverridingMethodSniff::class,
        PropertyTypeHintSniff::class,
        ReturnTypeHintSniff::class,
        UselessInheritDocCommentSniff::class,
        DisallowEmptySniff::class,
        SuperfluousTraitNamingSniff::class,
        ForbiddenTraits::class,
    ],
    'config' => [
        BinaryOperatorSpacesFixer::class         => [
            'align_double_arrow' => true,
            'align_equals'       => false,
        ],
        LineLengthSniff::class                   => [
            'lineLimit'         => 120,
            'absoluteLineLimit' => 120,
            'ignoreComments'    => false,
        ],
        MethodPerClassLimitSniff::class          => [
            'maxCount' => 15,
            'exclude'  => [
                'src/Entity',
                'src/Helper/PageCreatorConfig.php',
                'src/Helper/PageCreatorConfigInterface.php',
            ],
        ],
        FunctionLengthSniff::class               => [
            'maxLength' => 30,
            'exclude'   => [
                'src/Form',
                'src/Twig/MenuExtension.php',
                'src/Traits/NodeInnerJoinTrait.php',
                'src/Twig/AbstractLanguageExtension.php',
                'src/Helper/PageCreator.php',
                'src/Repository/AbstractPageRepository.php',
            ],
        ],
        ClassTraitAndInterfaceLengthSniff::class => [
            'exclude' => [
                'src/Entity',
            ],
        ],
        ReturnTypeHintSniff::class               => [
            'exclude' => [],
        ],
        VoidReturnFixer::class                   => [],
        CyclomaticComplexityIsHigh::class        => [
            'maxComplexity' => 14,
            'exclude'       => [
            ],
        ],
        UnusedParameterSniff::class              => [
            'exclude' => [
                'src/Command',
                'src/Listener/AbstractOnFlushListener.php',
                'src/Repository/AbstractFilteredPageRepository.php',
                'src/Helper/PageCreator.php',
            ],
        ],
        TodoSniff::class                         => [
            'exclude' => [
            ],
        ],
    ],
];
