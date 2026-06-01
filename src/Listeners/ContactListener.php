<?php

namespace RiseTechApps\Contact\Listeners;

use RiseTechApps\Contact\Events\ContactEvent;

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

            // Delega a persistência para o método da trait HasContacts
            $event->model->syncContacts($contacts);

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
}
