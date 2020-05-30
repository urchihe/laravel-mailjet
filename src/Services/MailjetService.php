<?php

namespace Admin\Mailjet\Services;

use \Mailjet\Resources;
use \Mailjet\Response;
use \Mailjet\Client;
use Admin\Mailjet\Contracts\MailjetServiceContract;
use Admin\Mailjet\Exception\MailjetException;
use Admin\Mailjet\Model\Campaign;
use Admin\Mailjet\Model\CampaignDraft;
use Admin\Mailjet\Model\ContactMetadata;
use Admin\Mailjet\Model\Contact;
use Admin\Mailjet\Model\ContactsList;
use Admin\Mailjet\Model\EventCallbackUrl;
use Admin\Mailjet\Model\Template;
use Admin\Mailjet\Transport\MailjetTransport;

class MailjetService implements MailjetServiceContract
{
    /**
     * @var int
     */
    const CONTACT_BATCH_SIZE = 1000;

    /**
     * Mailjet Client
     * @var \Mailjet\Client
     */
    private $client;
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var string
     */
    private $call;
    /**
     * @var array
     */
    private $options;
    /**
     * Instanciate the client whit the api key and api secret given in the configuration
     */
    public function __construct($key, $secret, $call = true, array $settings = [])
    {
        $this->client = new Client($key, $secret, $call, $settings);
        $this->key = $key;
        $this->secret = $secret;
        $this->call = $call;
        $this->options = $settings;
    }
    /**
     * Trigger a POST request
     *
     * @param array $resource Mailjet Resource/Action pair
     * @param array $args     Request arguments
     * @param array $options
     *
     * @return Response
     */
    public function post($resource, array $args = [], array $options = [])
    {
        $response = $this->client->post($resource, $args, $options);
        return $response;
    }
    /**
     * Trigger a GET request
     *
     * @param array $resource Mailjet Resource/Action pair
     * @param array $args     Request arguments
     * @param array $options
     *
     * @return Response
     */
    public function get($resource, array $args = [], array $options = [])
    {
        $response = $this->client->get($resource, $args, $options);
        return $response;
    }
    /**
     * Trigger a PUT request
     *
     * @param array $resource Mailjet Resource/Action pair
     * @param array $args     Request arguments
     * @param array $options
     *
     * @return Response
     */
    public function put($resource, array $args = [], array $options = [])
    {
        $response = $this->client->put($resource, $args, $options);
        return $response;
    }
    /**
     * Trigger a DELETE request
     *
     * @param array $resource Mailjet Resource/Action pair
     * @param array $args     Request arguments
     * @param array $options
     *
     * @return Response
     */
    public function delete($resource, array $args = [], array $options = [])
    {
        $response = $this->client->delete($resource, $args, $options);
        return $response;
    }

    /**
     * Get all list on your mailjet account
     * @param  array $filters Filters that will be use to filter the request.
     * @return Response
     */
    public function getAllLists($filters)
    {
        $response = $this->get(Resources::$Contactslist, ['filters' => $filters]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getAllLists() failed', $response);
        }
        return $response;
    }
    /**
     * Create a new list
     * @param  array $body array containing the list informations.
     * @return Response
     */
    public function createList($body)
    {
        $response = $this->post(Resources::$Contactslist, ['body' => $body]);
        if (!$response->success()) {
            $this->throwError('MailjetService:createList() failed', $response);
        }
        return $response;
    }
    /**
     * Get all list recipient on your mailjet account
     * @param  array $filters Filters that will be use to filter the request.
     * @return Response
     */
    public function getListRecipients($filters)
    {
        $response = $this->get(Resources::$Listrecipient, ['filters' => $filters]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getListRecipients() failed', $response);
        }
        return $response;
    }
    /**
     * Get single contact informations.
     * @param  int $id ID of the contact
     * @return Response
     */
    public function getSingleContact($id)
    {
        $response = $this->get(Resources::$Contact, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getSingleContact() failed', $response);
        }
        return $response;
    }
    /**
     * create a contact
     * @param  array $body array containing the list informations.
     */
    public function createContact($body)
    {
        $response = $this->post(Resources::$Contact, ['body' => $body]);
        if (!$response->success()) {
            $this->throwError('MailjetService:createContact() failed', $response);
        }
        return $response;
    }
    /**
     * create a listrecipient (relationship between contact and list)
     * @param  array $body array containing the list informations.
     * @return Response
     */
    public function createListRecipient($body)
    {
        $response = $this->post(Resources::$Listrecipient, ['body' => $body]);
        if (!$response->success()) {
            $this->throwError('MailjetService:createListRecipient() failed', $response);
        }
        return $response;
    }
    /**
     * edit a list recipient
     * @param  int $id   id of the list recipient
     * @param  array $body array containing the list informations.
     */
    public function editListRecipient($id, $body)
    {
        $response = $this->put(Resources::$Listrecipient, ['id' => $id, 'body' => $body]);
        if (!$response->success()) {
            $this->throwError('MailjetService:editListrecipient() failed', $response);
        }
        return $response;
    }
    // Campaigns Services

    /**
     * List campaigns resources available for this apikey
     * @return array
     */
    public function getAllCampaigns(array $filters = null)
    {
        $response = $this->get(Resources::$Campaign, ['filters' => $filters]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getAllCampaigns() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Access a given campaign resource
     * @param string $CampaignId
     * @return array
     */
    public function findByCampaignId($CampaignId)
    {
        $response = $this->get(Resources::$Campaign, ['id' => $CampaignId]);
        if (!$response->success()) {
            $this->throwError('MailjetService:findByCampaignId() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Update one specific campaign resource with a PUT request, providing the campaign's ID value
     * @param string $CampaignId
     * @return array
     */
    public function updateCampaign($CampaignId, Campaign $campaign)
    {
        $response = $this->put(Resources::$Campaign, ['id' => $CampaignId, 'body' => $campaign->format()]);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateCampaign() failed', $response);
        }
        return $response->getData();
    }

    //CampaignDraftServices
    /**
     * List campaigndraft resources available for this apikey
     * @return array
     */
    public function getAllCampaignDrafts(array $filters = null)
    {
        $response = $this->get(
            Resources::$Campaigndraft,
            ['filters' => $filters]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService :getAllCampaignDrafts() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Access a given campaigndraft resource
     * @param string $CampaignId
     * @return array
     */
    public function findByCampaignDraftId($CampaignId)
    {
        $response = $this->get(
            Resources::$Campaigndraft,
            ['id' => $CampaignId]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:findByCampaignDraftId() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * create a new fresh CampaignDraft
     * @param Campaigndraft $campaignDraft
     */
    public function createCampaignDraft(CampaignDraft $campaignDraft)
    {
        $response = $this->post(
            Resources::$Campaigndraft,
            ['body' => $campaignDraft->format()]
        );
        if (!$response->success()) {
            $this->throwError('MailjetService:createCampaignDraft() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Update one specific campaigndraft resource
     * @param int $CampaignId
     * @param Campaigndraft $campaignDraft
     */
    public function updateCampaignDraft($CampaignId, CampaignDraft $campaignDraft)
    {
        $response = $this->put(
            Resources::$Campaigndraft,
            ['id' => $CampaignId, 'body' => $campaignDraft->format()]
        );
        if (!$response->success()) {
            $this->throwError('MailjetService:updateCampaignDraft() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Return the text and html contents of the campaigndraft
     * @param string $id
     * @return array
     */
    public function getDetailContentCampaignDraft($id)
    {
        $response = $this->get(
            Resources::$CampaigndraftDetailcontent,
            ['id' => $id]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:getDetailContentCampaignDraft() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Creates the content of a  campaigndraft
     * @param string $id
     * @return array
     */
    public function createDetailContentCampaignDraft($id, $contentData)
    {
        $response = $this->post(
            Resources::$CampaigndraftDetailcontent,
            ['id' => $id, 'body' => $contentData]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:createDetailContentCampaignDraft() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Return the date of the scheduled sending of the campaigndraft
     * @param string $CampaignId
     * @return array
     */
    public function getSchedule($CampaignId)
    {
        $response = $this->get(
            Resources::$CampaigndraftSchedule,
            ['id' => $CampaignId]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:getSchedule() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Schedule when the campaigndraft will be sent
     * @param string $CampaignId
     * @param string $date
     * @return array
     */
    public function scheduleCampaign($CampaignId, $date)
    {
        $response = $this->post(
            Resources::$CampaigndraftSchedule,
            ['id' => $CampaignId, 'body' => $date]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:scheduleCampaign() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Update the date when the campaigndraft will be sent
     * @param string $CampaignId
     * @param string $date
     * @return array
     */
    public function updateCampaignSchedule($CampaignId, $date)
    {
        $response = $this->put(
            Resources::$CampaigndraftSchedule,
            ['id' => $CampaignId, 'body' => $date]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:updateCampaignSchedule() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Cancel a future sending
     * @param string $CampaignId
     * @return array
     */
    public function removeSchedule($CampaignId)
    {
        $response = $this->delete(
            Resources::$CampaigndraftSchedule,
            ['id' => $CampaignId]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:removeSchedule() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Send the campaign immediately
     * @param string $CampaignId
     * @return array
     */
    public function sendCampaign($CampaignId)
    {
        $response = $this->post(
            Resources::$CampaigndraftSend,
            ['id' => $CampaignId]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:sendCampaign() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Return the status of a CampaignDraft
     * @param string $CampaignId
     * @return array
     */
    public function getCampaignStatus($CampaignId)
    {
        $response = $this->get(
            Resources::$CampaigndraftStatus,
            ['id' => $CampaignId]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:getCampaignStatus() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * An action to test a CampaignDraft.
     * @param string $CampaignId
     * @param array $recipients
     * @return array
     */
    public function testCampaign($CampaignId, $recipients)
    {
        $response = $this->post(
            Resources::$CampaigndraftTest,
            ['id' => $CampaignId, 'body' => $recipients]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:testCampaign() failed',
                $response
            );
        }
        return $response->getData();
    }

    //ContactMetaDataServices for mailjet APIs
    /**
     * Retrieve all ContactMetadata
     * @return array
     */
    public function getAllContactMetadata()
    {
        $response = $this->get(Resources::$Contactmetadata);
        if (!$response->success()) {
            $this->throwError('MailjetService:getAllContactMetadata() failed', $response);
        }
        return $response->getData();
    }

    /**
     * Retrieve one ContactMetadata
     * @param string $id
     * @return array
     */
    public function getContactMetadata($id)
    {
        $response = $this->get(Resources::$Contactmetadata, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getContactMetadata() failed', $response);
        }
        return $response->getData();
    }

    /**
     * create a new fresh ContactMetadata
     * @param ContactMetadata $contactMetadata
     */
    public function createContactMetadata(ContactMetadata $contactMetadata)
    {
        $response = $this->post(Resources::$Contactmetadata, ['body' => $contactMetadata->format()]);
        if (!$response->success()) {
            $this->throwError('MailjetService:createContactMetadata() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Update one ContactMetadata
     * @param int $id
     * @param ContactMetadata $contactMetadata
     */
    public function updateContactMetadata($id, ContactMetadata $contactMetadata)
    {
        $response = $this->put(Resources::$Contactmetadata, ['id' => $id,'body' => $contactMetadata->format()]);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateContactMetadata() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Delete one ContactMetadata
     * @param int $id
     */
    public function deleteContactMetadata($id)
    {
        $response = $this->delete(Resources::$Contactmetadata, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:deleteContactMetadata() failed', $response);
        }
        return $response->getData();
    }

    //ContactsListServices
    /**
     * create a new fresh Contact to listId
     * @param string $listId
     * @param Contact $contact
     * @param string $action
     */
    public function createContactsList($listId, Contact $contact, $action = Contact::ACTION_ADDFORCE)
    {
        $contact->setAction($action);
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:createContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * update a Contact to listId
     * @param string $listId
     * @param Contact $contact
     * @param string $action
     */
    public function updateContactsList($listId, Contact $contact, $action = Contact::ACTION_ADDNOFORCE)
    {
        $contact->setAction($action);
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * re/subscribe a Contact to listId
     * @param string $listId
     * @param Contact $contact
     * @param bool $force
     */
    public function subscribeContactsList($listId, Contact $contact, $force = true)
    {
        if ($force) {
            $contact->setAction(Contact::ACTION_ADDFORCE);
        } else {
            $contact->setAction(Contact::ACTION_ADDNOFORCE);
        }
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:subscribeContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * unsubscribe a Contact from listId
     * @param string $listId
     * @param Contact $contact
     */
    public function unsubscribeContactsList($listId, Contact $contact)
    {
        $contact->setAction(Contact::ACTION_UNSUB);
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:unsubscribeContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Delete a Contact from listId
     * @param string $listId
     * @param Contact $contact
     */
    public function deleteContactsList($listId, Contact $contact)
    {
        $contact->setAction(Contact::ACTION_REMOVE);
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:deleteContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Change email a Contact
     * @param string $listId
     * @param Contact $contact
     * @param string $oldEmail
     */
    public function updateEmailContactsList($listId, Contact $contact, $oldEmail)
    {
        // get old contact properties
        $response = $this->get(Resources::$Contactdata, ['id' => $oldEmail]);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateEmailContactList() failed', $response);
        }
        // copy contact properties
        $oldContactData = $response->getData();
        if (isset($oldContactData[0])) {
            $contact->setProperties($oldContactData[0]['Data']);
        }
        // add new contact
        $contact->setAction(Contact::ACTION_ADDFORCE);
        $response = $this->exec($listId, $contact);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateEmailContactList() failed', $response);
        }
        // remove old
        $oldContact = new Contact($oldEmail);
        $oldContact->setAction(Contact::ACTION_REMOVE);
        $response = $this->exec($listId, $oldContact);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateEmailContactList() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Import many contacts to a list
     * https://dev.mailjet.com/email-api/v3/contactslist-managemanycontacts/
     * @param  ContactsList $contactsList
     * @return array
     */
    public function uploadManyContactsList(ContactsList $contactsList)
    {
        $batchResults = [];
        // we send multiple smaller requests instead of a bigger one
        $contactChunks = array_chunk($contactsList->getContacts(), self::CONTACT_BATCH_SIZE);
        foreach ($contactChunks as $contactChunk) {
            // create a sub-contactList to divide large request
            $subContactsList = new ContactsList($contactsList->getListId(), $contactsList->getAction(), $contactChunk);
            $currentBatch = $this->post(
                Resources::$ContactslistManagemanycontacts,
                ['id' => $subContactsList->getListId(), 'body' => $subContactsList->format()]
            );
            if ($currentBatch->success()) {
                array_push($batchResults, $currentBatch->getData()[0]);
            } else {
                $this->throwError('MailjetService:uploadManyContactsList() failed', $currentBatch);
            }
        }
        return $batchResults;
    }
    /**
    * An action for adding a contact to a contact list. Only POST is supported.
    * The API will internally create the new contact if it does not exist,
    * add or update the name and properties.
    * The properties have to be defined before they can be used.
    * The API then adds the contact to the contact list with active=true and
    * unsub=specified value if it is not already in the list,
    * or updates the entry with these values. On success,
    * the API returns a packet with the same format but with all properties available
    * for that contact.
    * @param string $listId
    * @param Contact $contact
    */
    private function exec($listId, Contact $contact)
    {
        return $this->post(
            Resources::$ContactslistManagecontact,
            ['id' => $listId, 'body' => $contact->format()]
        );
    }

    //EventCallback
     /**
     * Retrieve all EventCallbackUrl
     * @return array
     */
    public function getAllEventCallback()
    {
        $response = $this->get(Resources::$Eventcallbackurl);
        if (!$response->success()) {
            $this->throwError('MailjetService:getAllEventCallback() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Retrieve one EventCallbackUrl
     * @param string $id
     * @return array
     */
    public function getEventCallback($id)
    {
        $response = $this->get(Resources::$Eventcallbackurl, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getEventCallback() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Create one EventCallbackUrl
     * @param EventCallbackUrl $eventCallbackUrl
     * @return array
     */
    public function createEventCallback(EventCallbackUrl $eventCallbackUrl)
    {
        $response = $this->post(Resources::$Eventcallbackurl, ['body' => $eventCallbackUrl->format()]);
        if (!$response->success()) {
            $this->throwError('MailjetService:createEventCallback() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Update one EventCallbackUrl
     * @param string $id
     * @param EventCallbackUrl $eventCallbackUrl
     * @return array
     */
    public function updateEventCallback($id, EventCallbackUrl $eventCallbackUrl)
    {
        $response = $this->put(Resources::$Eventcallbackurl, ['id' => $id, 'body' => $eventCallbackUrl->format()]);
        if (!$response->success()) {
            $this->throwError('MailjetService:updateEventCallback() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Delete one EventCallbackUrl
     * @param string $id
     * @return array
     */
    public function deleteEventCallback($id)
    {
        $response = $this->delete(Resources::$Eventcallbackurl, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:deleteEventCallback() failed', $response);
        }
        return $response->getData();
    }
    //Template Mailjet Services

        /**
     * List template resources available for this apikey, use a GET request.
     * Alternatively, you may want to add one or more filters.
     * @param array $filters
     * @return array
     */
    public function getAllTemplate(array $filters = null)
    {
        $response = $this->get(
            Resources::$Template,
            ['filters' => $filters]
        );
        if (!$response->success()) {
            $this->throwError('MailjetService:getAllTemplate() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Access a given template resource, use a GET request, providing the template's ID value
     * @param string $id
     * @return array
     */
    public function getTemplate($id)
    {
        $response = $this->get(Resources::$Template, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:getTemplate() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Add a new template resource with a POST request.
     * @param Template $Template
     */
    public function createTemplate(Template $Template)
    {
        $response = $this->post(
            Resources::$Template,
            ['body' => $Template->format()]
        );
        if (!$response->success()) {
            $this->throwError('MailjetService:createTemplate() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Update one specific template resource with a PUT request, providing the template's ID value
     * @param int $id
     * @param Template $Template
     */
    public function updateTemplate($id, Template $Template)
    {
        $response = $this->put(
            Resources::$Template,
            ['id' => $id, 'body' => $Template->format()]
        );
        if (!$response->success()) {
            $this->throwError('MailjetService:updateTemplate() failed', $response);
        }
        return $response->getData();
    }
    /**
     * delete a given template
     * @param string $id
     * @return array
     */
    public function deleteTemplate($id)
    {
        $response = $this->delete(Resources::$Template, ['id' => $id]);
        if (!$response->success()) {
            $this->throwError('MailjetService:deleteTemplate() failed', $response);
        }
        return $response->getData();
    }
    /**
     * Return the text and html contents of the Template
     * @param string $id
     * @return array
     */
    public function getDetailContentTemplate($id)
    {
        $response = $this->get(
            Resources::$TemplateDetailcontent,
            ['id' => $id]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:getDetailContentTemplate() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Creates the content of a  Template
     * @return array
     */
    public function createDetailContentTemplate($id, $contentData)
    {
        $response = $this->post(
            Resources::$TemplateDetailcontent,
            ['id' => $id, 'body' => $contentData]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:createDetailContentTemplate() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Deletes the content of a  Template
     * @return array
     */
    public function deleteDetailContentTemplate($id)
    {
        $nullContent = null;
        $response    = $this->post(
            Resources::$TemplateDetailcontent,
            ['id' => $id, 'body' => $nullContent]
        );
        if (!$response->success()) {
            $this->throwError(
                'MailjetService:deleteDetailContentTemplate() failed',
                $response
            );
        }
        return $response->getData();
    }
    /**
     * Retrieve Mailjet\Client
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
    /**
     * Helper to throw error
     * @param  string $title
     * @param  Response $response
     */
    private function throwError($title, Response $response)
    {
        throw new MailjetException(0, $title, $response);
    }

    /**
     * @param array $body
     * @return mixed
     */
    public function sendBulk(array $body)
    {
        $transport = new MailjetTransport(
            new \Swift_Events_SimpleEventDispatcher(),
            $this->key,
            $this->secret,
            $this->call,
            $this->options
        );
        return $result = $transport->bulkSend($body);
    }

    /**
     * @param \Swift_Message $body
     * @return mixed
     */
    public function send(\Swift_Message $body)
    {
        $transport = new MailjetTransport(
            new \Swift_Events_SimpleEventDispatcher(),
            $this->key,
            $this->secret,
            $this->call,
            $this->options
        );
        return $result = $transport->send($body);
    }
}
