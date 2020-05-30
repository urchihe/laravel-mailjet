<?php

namespace Admin\Mailjet\Contracts;

use Admin\Mailjet\Model\Campaign;
use Admin\Mailjet\Model\CampaignDraft;
use Admin\Mailjet\Model\ContactMetadata;
use Admin\Mailjet\Model\Contact;
use Admin\Mailjet\Model\ContactsList;
use Admin\Mailjet\Model\EventCallbackUrl;
use Admin\Mailjet\Model\Template;

interface MailjetServiceContract
{

    public function getAllCampaigns(array $filters = null);

    public function findByCampaignId($CampaignId);

    public function updateCampaign($CampaignId, Campaign $campaign);

    public function getAllCampaignDrafts(array $filters = null);

    public function findByCampaignDraftId($CampaignId);

    public function createCampaignDraft(CampaignDraft $campaignDraft);

    public function updateCampaignDraft($CampaignId, CampaignDraft $campaignDraft);

    public function getDetailContentCampaignDraft($id);

    public function createDetailContentCampaignDraft($id, $contentData);

    public function getSchedule($CampaignId);

    public function scheduleCampaign($CampaignId, $date);

    public function updateCampaignSchedule($CampaignId, $date);

    public function removeSchedule($CampaignId);

    public function sendCampaign($CampaignId);

    public function testCampaign($CampaignId, $recipients);

    public function getCampaignStatus($CampaignId);

    public function createContact($body);

    public function getAllContactMetadata();

    public function getContactMetadata($id);

    public function createContactMetadata(ContactMetadata $contactMetadata);

    public function updateContactMetadata($id, ContactMetadata $contactMetadata);

    public function deleteContactMetadata($id);

    public function createContactsList(
        $listId,
        Contact $contact,
        $action = Contact::ACTION_ADDFORCE
    );

    public function updateContactsList(
        $listId,
        Contact $contact,
        $action = Contact::ACTION_ADDNOFORCE
    );
                           
    public function subscribeContactsList($listId, Contact $contact, $force = true);

    public function unsubscribeContactsList($listId, Contact $contact);

    public function deleteContactsList($listId, Contact $contact);

    public function updateEmailContactsList($listId, Contact $contact, $oldEmail);

    public function uploadManyContactsList(ContactsList $contactsList);

    public function getAllEventCallback();

    public function getEventCallback($id);

    public function createEventCallback(EventCallbackUrl $eventCallbackUrl);

    public function updateEventCallback($id, EventCallbackUrl $eventCallbackUrl);

    public function deleteEventCallback($id);

    public function getAllTemplate(array $filters = null);

    public function getTemplate($id);

    public function createTemplate(Template $Template);

    public function updateTemplate($id, Template $Template);

    public function deleteTemplate($id);

    public function getDetailContentTemplate($id);

    public function createDetailContentTemplate($id, $contentData);

    public function deleteDetailContentTemplate($id);
}
