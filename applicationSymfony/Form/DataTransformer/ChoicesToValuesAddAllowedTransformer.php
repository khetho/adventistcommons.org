<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChoicesToValuesAddAllowedTransformer implements DataTransformerInterface
{
    private $choiceList;

    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * @param array $array
     * @return array
     */
    public function transform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!\is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $output = [];
        foreach ($array as $value) {
            $choices = $this->choiceList->getValuesForChoices([$value]);
            $thisOutput = null;
            if ($choices) {
                $thisOutput = $choices[0];
            } elseif (is_string($value)) {
                $thisOutput = $value;
            }
            if ($thisOutput === null) {
                throw new TransformationFailedException('Value must be listed as a basic option, or a string');
            }
            $output[] = $thisOutput;
        }

        return $output;
    }

    /**
     * @param array $array
     * @return array
     */
    public function reverseTransform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!\is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $output = [];
        foreach ($array as $value) {
            $choices = $this->choiceList->getChoicesForValues([$value]);
            $thisOutput = null;
            if ($choices) {
                $thisOutput = $choices[0];
            } elseif (is_string($value)) {
                $thisOutput = $value;
            }
            if ($thisOutput === null) {
                throw new TransformationFailedException('Value must be listed as a basic option, or a string');
            }
            $output[] = $thisOutput;
        }

        return $output;
    }
}
