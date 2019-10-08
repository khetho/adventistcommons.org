<?php
namespace AdventistCommons\Import\Idml;
class IDMLlib
{
  private $file;

  public function __construct(IDMLfile $file)
  {
    $this->file = $file;

    $this->init();
  }

  private function init()
  {


  }
  /**
   * returns the content of a tag by the selecting name
   * @param $name
   * @param bool $html
   */
  public function getContentByTagName($name, $html = true)
  {
    $arr = array();
    $stories = $this->file->getContentFile('Stories/Story_u1ab.xml');
    $collection = new IDMLcontentCollection($this->file, $stories);
    return $collection->getContentByTagName($name);

  }

  public function getMyContent($name, $html = true)
  {
    $arr = array();
    foreach ($this->file->structure as $key => $value) {
        if(strpos($key, 'Story_') != false){
          $stories = $this->file->getContentFile($key);
          $collection = new IDMLcontentCollection($this->file, $stories);
          $arr[] = $collection->getMyContent($name);
        }
    }
    
    return $arr;
  }

  /**
   * Returns the content tags
   * @return array
   */
  public function getContentTags()
  {
    $arr = array();
    $content = $this->file->getContentFile('XML/Tags.xml', true);
    $tag = new IDMLtagCollection($this->file, $content);

    $tags = $tag->getTags();
    /** @var IDMLtag $tag */
    foreach($tags as $tag)
    {
      $arr[] = $tag->getName();
    }
    return $arr;
  }
}
