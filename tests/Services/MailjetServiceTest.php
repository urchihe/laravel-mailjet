<?php


class MailjetServiceTest extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    public function testFacade()
    {
        $this->assertTrue(method_exists(app('Mailjet'), 'get'));
        $this->assertTrue(method_exists(app('Mailjet'), 'post'));
        $this->assertTrue(method_exists(app('Mailjet'), 'put'));
        $this->assertTrue(method_exists(app('Mailjet'), 'delete'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getAllLists'));
        $this->assertTrue(method_exists(app('Mailjet'), 'createList'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getListRecipients'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getSingleContact'));
        $this->assertTrue(method_exists(app('Mailjet'), 'createContact'));
        $this->assertTrue(method_exists(app('Mailjet'), 'createListRecipient'));
        $this->assertTrue(method_exists(app('Mailjet'), 'editListRecipient'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getClient'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getAllCampaigns'));
        $this->assertTrue(method_exists(app('Mailjet'), 'findByCampaignId'));
        $this->assertTrue(method_exists(app('Mailjet'), 'updateCampaign'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getAllCampaignDrafts'));
        $this->assertTrue(method_exists(app('Mailjet'), 'findByCampaignDraftId'));
        $this->assertTrue(method_exists(app('Mailjet'), 'createCampaignDraft'));
        $this->assertTrue(method_exists(app('Mailjet'), 'updateCampaignDraft'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getDetailContentCampaignDraft'));
        $this->assertTrue(method_exists(app('Mailjet'), 'getSchedule'));
        $this->assertTrue(method_exists(app('Mailjet'), 'scheduleCampaign'));
    }

    public function testCanUseClient()
    {
        $client = Urchihe\LaravelMailjet\Facades\Mailjet::getClient();
        $this->assertEquals("Mailjet\Client", get_class($client));
    }



    protected function getPackageAliases($app)
    {
        return [
            'Mailjet' => \Urchihe\LaravelMailjet\Facades\Mailjet::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('services.mailjet.key', 'ABC123456');
        $app['config']->set('services.mailjet.secret', 'ABC123456');
    }

    protected function getPackageProviders($app)
    {
        return ['\Urchihe\LaravelMailjet\MailjetServiceProvider'];
    }
}
