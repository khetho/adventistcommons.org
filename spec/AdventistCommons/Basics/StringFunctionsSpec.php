<?php

namespace spec\AdventistCommons\Basics;

use AdventistCommons\Basics\StringFunctions;
use PhpSpec\ObjectBehavior;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class StringFunctionsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StringFunctions::class);
    }

    public function it_should_not_touch_string_if_short()
    {
        $string = 'a23456789 b234567890';
        $this->limit($string, 20, 0)
             ->shouldReturn($string);
    }

    public function it_should_reduce_string()
    {
        $string = 'a23456789 b23456789 c1';
        $this->limit($string, 18, 0, '…')
            ->shouldReturn('a23456789 b234567…');
    }

    public function it_should_stop_a_spaceless_string()
    {
        $string = 'a234567890b23456789';
        $this->limit($string, 11, 1, '…')
            ->shouldReturn('a234567890…');
    }

    public function it_should_accept_flexibility_at_end()
    {
        $string = 'a23456789 b23456789';
        $this->limit($string, 19, 2, '…')
            ->shouldReturn($string);
    }

    public function it_should_accept_flexibility_at_separation()
    {
        $string = 'a23456789 b23456789 c234';
        $this->limit($string, 20, 3, '…')
            ->shouldReturn('a23456789 b23456789…');
    }

    public function it_should_keep_last_separator()
    {
        $string = 'a23456789 b23456789 c234';
        $this->limit($string, 21, 4, '…')
            ->shouldReturn('a23456789 b23456789 …');
    }

    public function it_should_limit_flexibility()
    {
        $string = 'a23456789 b23456789';
        $this->limit($string, 18, 7, '…')
            ->shouldReturn('a23456789 b234567…');
    }

    public function it_should_have_40_10_ellipsis_as_default()
    {
        $string = 'a23456789 b23456789 c23456789 d23456789 e1';
        $this->limit($string)
            ->shouldReturn('a23456789 b23456789 c23456789 d23456789…');
    }
}
