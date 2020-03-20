<?php
namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Basics\StringFunctions;

class XpathValidator
{
    private $stringFunctions;

    public function __construct(StringFunctions $stringFunctions)
    {
        $this->stringFunctions = $stringFunctions;
    }

    public function validateSameStyle(\DOMXPath $xpath): array
    {
        $errors = [];
        $appliedStyles = [
            'Text',
            '$ID/[No character style]',
        ];
        foreach ($appliedStyles as $appliedStyle) {
            $query = sprintf(
                '//CharacterStyleRange[@AppliedCharacterStyle="CharacterStyle/%s"]/following-sibling::CharacterStyleRange[@AppliedCharacterStyle="CharacterStyle/%s"]',
                $appliedStyle,
                $appliedStyle
            );
            /** @var \DOMNodeList $entries */
            $entries = $xpath->query($query);
            /** @var \DOMElement $entry */
            foreach ($entries as $entry) {
                if (!$entry->textContent) {
                    continue;
                }
                $errors[] = sprintf('Two following paragraphs have the same style : «%s».', $this->stringFunctions->limit($entry->textContent), 40);
            }
        }

        return $errors;
    }

    public function validateParagraphSeparators(\DOMXPath $xpath): array
    {
        $errors = [];
        $query = '//Content/preceding-sibling::*[1][name()="Content"]';   // equivalent to CSS selector : Content + Content
        /** @var \DOMNodeList $entries */
        $entries = $xpath->query($query);
        /** @var \DOMElement $entry */
        foreach ($entries as $entry) {
            $errors[] = sprintf('Two following paragraphs are not separated by a new line : «%s».', $this->stringFunctions->limit($entry->textContent), 40);
        }

        return $errors;
    }
}
