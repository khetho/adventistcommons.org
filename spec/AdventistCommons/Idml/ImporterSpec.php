<?php

namespace spec\AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;
use AdventistCommons\Idml\Importer;
use AdventistCommons\Idml\SectionPersisterInterface;
use AdventistCommons\Idml\ContentPersisterInterface;
use phpDocumentor\Reflection\Types\Void_;
use PhpSpec\ObjectBehavior;

class ImporterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Importer::class);
    }

    public function it_should_import_holder(
        SectionPersisterInterface $sectionPersister,
        ContentPersisterInterface $contentPersister,
        Holder $holder,
        Section $section,
        Story $story
    ) {
        $this->beConstructedWith($sectionPersister, $contentPersister);
        $story->getKey()->willReturn('storyKey');
        $section->getName()->willReturn('sectionName');
        $section->getStory()->willReturn($story);
        $section->getContents()->willReturn([]);
        $holder->getSections()->willReturn([$section]);
        $section->setDbId(null)->checkPrediction();
        $this->import($holder, 1);
        $sectionPersister->create(
            [
                'product_id' => 1,
                'name'       => 'sectionName',
                'order'      => 0,
                'story_key'  => 'storyKey',
            ]
        )->shouldHaveBeenCalled();
    }
}
