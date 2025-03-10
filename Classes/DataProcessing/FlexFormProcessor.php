<?php
declare(strict_types = 1);

namespace Brotkrueml\CodeHighlight\DataProcessing;

/*
 * This file is part of the "codehighlight" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * This data processor can be used for processing data for the content elements which have flexform contents in one field
 *
 * Example TypoScript configuration:
 * 10 = Brotkrueml\CodeHighlight\DataProcessing\FlexFormProcessor
 * 10 {
 *   if.isTrue.field = pi_flexform
 *   fieldName = pi_flexform
 *   as = flexform
 * }
 *
 * whereas "flexform" can be used as a variable {flexform} inside Fluid to fetch values.
 */
class FlexFormProcessor implements DataProcessorInterface
{
    /** @var FlexFormService */
    private $flexFormService;

    public function __construct(FlexFormService $flexFormService = null)
    {
        $this->flexFormService = $flexFormService ?? GeneralUtility::makeInstance(FlexFormService::class);
    }

    public function process(
        ContentObjectRenderer $contentObjectRenderer,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$contentObjectRenderer->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $targetVariableName = $contentObjectRenderer->stdWrapValue('as', $processorConfiguration, 'flexform');
        $fieldName = $contentObjectRenderer->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');

        $processedData[$targetVariableName] = $this->flexFormService->convertFlexFormContentToArray($contentObjectRenderer->data[$fieldName]);

        return $processedData;
    }
}
