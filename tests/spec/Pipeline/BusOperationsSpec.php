<?php

namespace spec\PSB\Core\Pipeline;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PSB\Core\Pipeline\BusOperations;
use PSB\Core\Pipeline\BusOperationsContextFactory;
use PSB\Core\Pipeline\Incoming\IncomingContext;
use PSB\Core\Pipeline\Outgoing\StageContext\OutgoingPublishContext;
use PSB\Core\Pipeline\Outgoing\StageContext\OutgoingReplyContext;
use PSB\Core\Pipeline\Outgoing\StageContext\OutgoingSendContext;
use PSB\Core\Pipeline\Outgoing\StageContext\SubscribeContext;
use PSB\Core\Pipeline\Outgoing\StageContext\UnsubscribeContext;
use PSB\Core\Pipeline\Pipeline;
use PSB\Core\Pipeline\PipelineFactory;
use PSB\Core\Pipeline\PipelineModifications;
use PSB\Core\Pipeline\PipelineStageContext;
use PSB\Core\PublishOptions;
use PSB\Core\ReplyOptions;
use PSB\Core\SendOptions;
use PSB\Core\SubscribeOptions;
use PSB\Core\UnsubscribeOptions;
use PSB\Core\UuidGeneration\UuidGeneratorInterface;

/**
 * @mixin BusOperations
 */
class BusOperationsSpec extends ObjectBehavior
{
    /**
     * @var PipelineFactory
     */
    private $pipelineFactoryMock;

    /**
     * @var BusOperationsContextFactory
     */
    private $busOperationsContextFactoryMock;

    /**
     * @var PipelineModifications
     */
    private $pipelineModificationsMock;

    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGeneratorMock;

    function let(
        PipelineFactory $pipelineFactory,
        BusOperationsContextFactory $busOperationsContextFactory,
        PipelineModifications $pipelineModifications,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->pipelineFactoryMock = $pipelineFactory;
        $this->busOperationsContextFactoryMock = $busOperationsContextFactory;
        $this->pipelineModificationsMock = $pipelineModifications;
        $this->uuidGeneratorMock = $uuidGenerator;

        $this->beConstructedWith(
            $pipelineFactory,
            $busOperationsContextFactory,
            $pipelineModifications,
            $uuidGenerator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PSB\Core\Pipeline\BusOperations');
    }

    function it_sends_a_message_with_autogenerated_id(
        SendOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        OutgoingSendContext $sendContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn(null);
        $this->uuidGeneratorMock->generate()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createSendContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($sendContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($sendContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId($uuid)->shouldBeCalled();
        $pipeline->invoke($sendContext)->shouldBeCalled();

        $this->send($irrelevantMessage, $options, $parentContext);
    }

    function it_sends_a_message_with_preset_id(
        SendOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        OutgoingSendContext $sendContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createSendContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($sendContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($sendContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId(Argument::any())->shouldNotBeCalled();
        $pipeline->invoke($sendContext)->shouldBeCalled();

        $this->send($irrelevantMessage, $options, $parentContext);
    }

    function it_sends_a_message_to_local_endpoint(
        SendOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        OutgoingSendContext $sendContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createSendContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($sendContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($sendContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->routeToLocalEndpointInstance()->shouldBeCalled();
        $pipeline->invoke($sendContext)->shouldBeCalled();

        $this->sendLocal($irrelevantMessage, $options, $parentContext);
    }

    function it_publishes_a_message_with_autogenerated_id(
        PublishOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        OutgoingPublishContext $publishContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn(null);
        $this->uuidGeneratorMock->generate()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createPublishContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($publishContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($publishContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId($uuid)->shouldBeCalled();
        $pipeline->invoke($publishContext)->shouldBeCalled();

        $this->publish($irrelevantMessage, $options, $parentContext);
    }

    function it_publishes_a_message_with_preset_id(
        PublishOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        OutgoingPublishContext $publishContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createPublishContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($publishContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($publishContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId(Argument::any())->shouldNotBeCalled();
        $pipeline->invoke($publishContext)->shouldBeCalled();

        $this->publish($irrelevantMessage, $options, $parentContext);
    }

    function it_replies_with_a_message_with_autogenerated_id(
        ReplyOptions $options,
        Pipeline $pipeline,
        IncomingContext $parentContext,
        OutgoingReplyContext $replyContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn(null);
        $this->uuidGeneratorMock->generate()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createReplyContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($replyContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($replyContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId($uuid)->shouldBeCalled();
        $pipeline->invoke($replyContext)->shouldBeCalled();

        $this->reply($irrelevantMessage, $options, $parentContext);
    }

    function it_replies_with_a_message_with_preset_id(
        ReplyOptions $options,
        Pipeline $pipeline,
        IncomingContext $parentContext,
        OutgoingReplyContext $replyContext,
        $uuid
    ) {
        $irrelevantMessage = new \stdClass();
        $options->getMessageId()->willReturn($uuid);
        $this->busOperationsContextFactoryMock->createReplyContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($replyContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($replyContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $options->setMessageId(Argument::any())->shouldNotBeCalled();
        $pipeline->invoke($replyContext)->shouldBeCalled();

        $this->reply($irrelevantMessage, $options, $parentContext);
    }

    function it_subscribes_to_an_event(
        SubscribeOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        SubscribeContext $subscribeContext
    ) {
        $irrelevantMessage = new \stdClass();
        $this->busOperationsContextFactoryMock->createSubscribeContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($subscribeContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($subscribeContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $pipeline->invoke($subscribeContext)->shouldBeCalled();

        $this->subscribe($irrelevantMessage, $options, $parentContext);
    }

    function it_unsubscribes_from_an_event(
        UnsubscribeOptions $options,
        Pipeline $pipeline,
        PipelineStageContext $parentContext,
        UnsubscribeContext $unsubscribeContext
    ) {
        $irrelevantMessage = new \stdClass();
        $this->busOperationsContextFactoryMock->createUnsubscribeContext(
            $irrelevantMessage,
            $options,
            $parentContext
        )->willReturn($unsubscribeContext);
        $this->pipelineFactoryMock->createStartingWith(
            get_class($unsubscribeContext->getWrappedObject()),
            $this->pipelineModificationsMock
        )->willReturn($pipeline);

        $pipeline->invoke($unsubscribeContext)->shouldBeCalled();

        $this->unsubscribe($irrelevantMessage, $options, $parentContext);
    }
}
