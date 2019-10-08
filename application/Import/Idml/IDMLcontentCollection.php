<?php
namespace AdventistCommons\Import\Idml;
class IDMLcontentCollection
{
  private $rawXML;

  public function __construct($resource, $content)
  {
    $this->rawXML = $content;
  }

  public function getMyContent($name){
    $domXML = new \DOMDocument();
    $domXML->loadXML($this->rawXML);

    return (simplexml_load_string($this->rawXML));
  }

  public function getContentByTagName($name)
  {
    $domXML = new \DOMDocument();
    $domXML->loadXML($this->rawXML);

    $elements = $domXML->getElementsByTagName('XMLElement');

    /** @var DOMDocument $element */
    foreach($elements as $element)
    {
      /** @var DOMAttr $markupAttribute */
      $markupAttribute = $element->attributes->getNamedItem('MarkupTag');

      $arrTagName = explode('/',$markupAttribute->textContent);
      $tagName = $arrTagName[1];
      if($tagName == $name)
      {
        $content = new IDMLcontent($element);
        return $content->convertXMLtoHTML();
      }
    }
  }
  public function strip_tags_content($text, $tags = '', $invert = FALSE) 
    { 

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
        $tags = array_unique($tags[1]); 
        
        if(is_array($tags) AND count($tags) > 0) 
        { 
            if($invert == FALSE) 
            { 
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
            } 
            else 
            { 
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
            } 
        } 
        elseif($invert == FALSE) 
        { 
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
        } 
        return $text; 
    }
}
