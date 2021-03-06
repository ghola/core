<?php

namespace spec\PSB\Core\MessageMutation\Pipeline\Incoming;

use PhpSpec\ObjectBehavior;

use PSB\Core\MessageMutation\Pipeline\Incoming\IncomingPhysicalMessageMutationContext;

/**
 * @mixin IncomingPhysicalMessageMutationContext
 */
class IncomingPhysicalMessageMutationContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('body', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Core\MessageMutation\Pipeline\Incoming\IncomingPhysicalMessageMutationContext');
    }

    function it_contains_the_body_set_during_construction()
    {
        $this->getBody()->shouldReturn('body');
    }

    function it_contains_the_headers_set_during_Construction()
    {
        $this->getHeaders()->shouldReturn([]);
    }

    function it_allows_the_body_to_be_replaced()
    {
        $this->replaceBody('newbody');

        $this->getBody()->shouldReturn('newbody');
    }

    function it_allows_headers_to_be_set()
    {
        $this->setHeader('key', 'newvalue');

        $this->getHeaders()->shouldReturn(['key' => 'newvalue']);
    }

    function it_throws_on_null_body_on_construction()
    {
        $this->beConstructedWith(null, []);
        $this->shouldThrow('PSB\Core\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_throws_on_null_headers_on_construction()
    {
        $this->beConstructedWith('', null);
        $this->shouldThrow()->duringInstantiation();
    }

    function it_throws_on_null_body_on_replacement()
    {
        $this->shouldThrow('PSB\Core\Exception\InvalidArgumentException')->duringReplaceBody(null);
    }
}
