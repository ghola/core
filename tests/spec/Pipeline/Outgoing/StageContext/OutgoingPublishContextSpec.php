<?php

namespace spec\PSB\Core\Pipeline\Outgoing\StageContext;

use PhpSpec\ObjectBehavior;

use PSB\Core\Pipeline\Outgoing\OutgoingLogicalMessage;
use PSB\Core\Pipeline\Outgoing\StageContext\OutgoingPublishContext;
use PSB\Core\Pipeline\PendingTransportOperations;
use PSB\Core\Pipeline\PipelineStageContext;
use PSB\Core\PublishOptions;
use PSB\Core\Transport\IncomingPhysicalMessage;

/**
 * @mixin OutgoingPublishContext
 */
class OutgoingPublishContextSpec extends ObjectBehavior
{
    private $publishOptionsMock;
    private $messageMock;
    private $transportOperationsMock;
    private $incomingPhysicalMessageMock;

    function let(
        OutgoingLogicalMessage $message,
        IncomingPhysicalMessage $incomingPhysicalMessage,
        PendingTransportOperations $transportOperations,
        PipelineStageContext $parentContext
    ) {
        $this->publishOptionsMock = new PublishOptions();
        $this->publishOptionsMock->setMessageId('someid');
        $this->messageMock = $message;
        $this->incomingPhysicalMessageMock = $incomingPhysicalMessage;
        $this->transportOperationsMock = $transportOperations;
        $this->beConstructedWith(
            $message,
            $this->publishOptionsMock,
            $incomingPhysicalMessage,
            $transportOperations,
            $parentContext
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Core\Pipeline\Outgoing\StageContext\OutgoingPublishContext');
    }

    function it_contains_the_logical_message_set_at_construction()
    {
        $this->getLogicalMessage()->shouldReturn($this->messageMock);
    }

    function it_contains_the_publish_options_set_at_construction()
    {
        $this->getPublishOptions()->shouldReturn($this->publishOptionsMock);
    }

    function it_contains_the_incoming_physical_message_set_at_construction()
    {
        $this->getIncomingPhysicalMessage()->shouldReturn($this->incomingPhysicalMessageMock);
    }

    function it_contains_the_transport_operations_set_at_construction()
    {
        $this->getPendingTransportOperations()->shouldReturn($this->transportOperationsMock);
    }

    function it_contains_the_message_id_set_at_construction()
    {
        $this->getMessageId()->shouldReturn('someid');
    }

    function it_contains_the_headers_set_at_construction()
    {
        $this->getHeaders()->shouldReturn([]);
    }
}
