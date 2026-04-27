<?php

namespace RiseTechApps\Contact\Listeners;

use Illuminate\Support\Facades\DB;
use RiseTechApps\Contact\Events\ContactEvent;
use RiseTechApps\Contact\Models\Contact;

class ContactListener
{
    public function __construct()
    {
    }

    public function handle(ContactEvent $event): void
    {
        try {
            $contacts = $this->getContactsFromRequest($event);

            if (empty($contacts)) {
                return;
            }

            if (!is_null($event->model->getOriginal('deleted_at'))) {
                return;
            }

            DB::transaction(function () use ($event, $contacts) {
                $this->syncContacts($event->model, $contacts);
            });

        } catch (\Exception $exception) {
            logglyError()->exception($exception)
                ->withRequest($event->request)
                ->performedOn($event->model)
                ->log("Error registering contact");
        }
    }

    private function getContactsFromRequest(ContactEvent $event): array
    {
        if ($event->request->has('contacts')) {
            return $event->request->input('contacts');
        }

        if ($event->request->has('person.contacts')) {
            return $event->request->input('person.contacts');
        }

        return \RiseTechApps\Contact\Contact::getContact() ?? [];
    }

    private function syncContacts($model, array $contacts): void
    {
        $existingContacts = $model->contacts()->withTrashed()->get()->keyBy('id');
        $processedIds = [];
        $sortOrder = 0;

        foreach ($contacts as $contactData) {
            $sortOrder++;
            $contactData['sort_order'] = $sortOrder;
            $contactData['contact_type'] = get_class($model);
            $contactData['contact_id'] = $model->getKey();

            $contactId = $contactData['id'] ?? null;

            if ($contactId && isset($existingContacts[$contactId])) {
                // Update existing contact
                $existingContacts[$contactId]->update($contactData);
                $existingContacts[$contactId]->restoreIfTrashed();
                $processedIds[] = $contactId;
            } else {
                // Create new contact
                $newContact = Contact::create($contactData);
                $processedIds[] = $newContact->getKey();
            }
        }

        // Delete contacts that are no longer present
        $idsToDelete = $existingContacts->keys()->diff($processedIds);
        if ($idsToDelete->isNotEmpty()) {
            $model->contacts()->whereIn('id', $idsToDelete)->delete();
        }

        // Ensure only one primary contact
        $this->ensureSinglePrimaryContact($model, $processedIds);
    }

    private function ensureSinglePrimaryContact($model, array $contactIds): void
    {
        if (empty($contactIds)) {
            return;
        }

        $primaryContact = $model->contacts()
            ->whereIn('id', $contactIds)
            ->where('is_primary', true)
            ->first();

        if ($primaryContact) {
            // Remove primary flag from other contacts
            $model->contacts()
                ->whereIn('id', $contactIds)
                ->where('id', '!=', $primaryContact->getKey())
                ->update(['is_primary' => false]);
        } else {
            // Set first contact as primary if none is marked
            $firstContactId = $contactIds[0] ?? null;
            if ($firstContactId) {
                $model->contacts()
                    ->where('id', $firstContactId)
                    ->update(['is_primary' => true]);
            }
        }
    }
}
