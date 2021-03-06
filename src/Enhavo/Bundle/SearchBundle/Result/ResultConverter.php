<?php
/**
 * Created by PhpStorm.
 * User: gseidel
 * Date: 14.05.18
 * Time: 10:47
 */

namespace Enhavo\Bundle\SearchBundle\Result;

use Enhavo\Bundle\SearchBundle\Extractor\Extractor;
use Enhavo\Bundle\SearchBundle\Util\Highlighter;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ResultConverter
{
    /**
     * @var Highlighter
     */
    private $highlighter;

    /**
     * @var Extractor
     */
    private $extractor;

    public function __construct(Highlighter $highlighter, Extractor $extractor)
    {
        $this->highlighter = $highlighter;
        $this->extractor = $extractor;
    }

    public function convert($result, $searchTerm, ResultConfiguration $configuration = null)
    {
        if($configuration === null) {
            $configuration = new ResultConfiguration();
        }

        $data = [];
        foreach($result as $resultItem) {
            $resultData = new Result();

            $text = $this->getText($resultItem, $configuration);

            $text = $this->highlighter->highlight(
                $text,
                explode(' ', $searchTerm),
                $configuration->getLength(),
                $configuration->getStartTag(),
                $configuration->getCloseTag(),
                $configuration->getConcat()
            );

            $resultData->setText($text);
            $resultData->setTitle($this->guessTitle($resultItem));
            $resultData->setSubject($resultItem);

            $data[] = $resultData;
        }
        return $data;
    }

    private function getText($resultItem, ResultConfiguration $configuration)
    {
        $text = $this->extractor->extract($resultItem);
        $text = implode($configuration->getConcat(), $text);
        return $text;
    }

    private function guessTitle($resultItem)
    {
        $properties = ['title', 'name', 'headline', 'header'];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($properties as $property) {
            try {
                $value =  $accessor->getValue($resultItem, $property);
                if($value) {
                    return $value;
                }
            } catch (AccessException $e) {
                continue;
            }
        }
        return '';
    }
}