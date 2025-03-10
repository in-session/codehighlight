<?php
declare(strict_types = 1);

namespace Brotkrueml\CodeHighlight\Service;

/*
 * This file is part of the "codehighlight" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\LanguageService;

final class ProgrammingLanguages
{
    private const LL_PREFIX = 'LLL:EXT:codehighlight/Resources/Private/Language/ProgrammingLanguages.xlf:';

    private $languageService;

    public function __construct(LanguageService $languageService = null)
    {
        $this->languageService = $languageService ?? $GLOBALS['LANG'];
    }

    public function getTcaItems(array &$config): void
    {
        $config['items'] = [
            ['', ''],
        ];

        $availableLanguages = $this->getAvailableProgrammingLanguages();

        foreach ($availableLanguages as $language) {
            $config['items'][] = [
                $this->languageService->sL(static::LL_PREFIX . $language) ?: $language,
                $language
            ];
        }

        \usort($config['items'], function (array $a, array $b): int {
            $aci = \str_replace('.', '', \strtolower($a[0]));
            $bci = \str_replace('.', '', \strtolower($b[0]));

            return $aci <=> $bci;
        });
    }

    private function getAvailableProgrammingLanguages(): array
    {
        $path = __DIR__ . '/../../Resources/Private/PHP/AvailableProgrammingLanguages.php';
        if (\file_exists($path)) {
            $languages = require $path;

            return \array_merge($languages, $this->getAliasesForMarkup());
        }

        return [];
    }

    private function getAliasesForMarkup(): array
    {
        // @see components/prism-markup.js
        return ['html', 'mathml', 'svg', 'xml'];
    }
}
