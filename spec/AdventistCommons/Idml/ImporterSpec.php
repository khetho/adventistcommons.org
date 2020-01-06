<?php

namespace spec\AdventistCommons\Idml;

use App\Entity\Product;
use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;
use AdventistCommons\Idml\Importer;
use AdventistCommons\Idml\SectionPersisterInterface;
use AdventistCommons\Idml\ContentPersisterInterface;
use phpDocumentor\Reflection\Types\Void_;
use PhpSpec\ObjectBehavior;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ImporterSpec extends ObjectBehavior
{
    public function it_is_initializable(
        SectionPersisterInterface $sectionPersister,
        ContentPersisterInterface $contentPersister
    ) {
        $this->beConstructedWith($sectionPersister, $contentPersister);
        $this->shouldHaveType(Importer::class);
    }

    public function it_should_import_holder(
        SectionPersisterInterface $sectionPersister,
        ContentPersisterInterface $contentPersister,
        Holder $holder,
        Section $section,
        Story $story,
        Product $product
    ) {
        $this->beConstructedWith($sectionPersister, $contentPersister);
        $story->getKey()->willReturn('storyKey');
        $section->getName()->willReturn('sectionName');
        $section->getStory()->willReturn($story);
        $section->getContents()->willReturn([]);
        $holder->getSections()->willReturn([$section]);
        $product->getId()->willReturn(1);
        $this->import($holder, $product);
        $sectionPersister->create(
            [
                'product'   => $product,
                'name'      => 'sectionName',
                'order'     => 0,
                'story_key' => 'storyKey',
            ]
        )->shouldHaveBeenCalled();
    }
}
