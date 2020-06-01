<?php


namespace Transports;

use Illuminate\Mail\Message;
use Illuminate\Support\Arr;
use Swift_Events_SimpleEventDispatcher;
use Swift_Message;
use Urchihe\LaravelMailjet\Transport\MailjetTransport;


class MailjetTransportTest extends \Orchestra\Testbench\TestCase
{

    /**
     * @var MailjetTransport
     */
    protected $transport;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiKey = env('MAILJET_APIKEY');
        $this->apiSecret = env('MAILJET_APISECRET');
        $this->transport = new MailjetTransport(new Swift_Events_SimpleEventDispatcher(),$this->apiKey,$this->apiSecret);
    }


    public function testSend()
    {
        $message = new Message($this->getMessage());
        $message->from(env('MAILJET_FROMEMAIL'), env('MAILJET_FROMNAME'))
            ->to(env('MAILJET_TOEMAIL'));
        $res = $this->transport->send($message->getSwiftMessage());
        $this->assertEquals(1, $res);
    }

    public function testBulkSend()
    {
        $messages = [];
        $message = $this->getMessage()
             ->setFrom(env('MAILJET_FROMEMAIL'), env('MAILJET_FROMNAME'))
            ->setTo(env('MAILJET_TOEMAIL'));
        $messages[] = $message;
        $res = $this->transport->bulkSend($messages);
        $this->assertEquals(1, $res);
    }

    /**
    * @return Swift_Message
    */
    private function getMessage()
    {
        return new Swift_Message('Test subject', 'Test body.');
    }

}