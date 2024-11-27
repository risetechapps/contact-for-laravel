<?php

namespace RiseTechApps\Contact\Listeners;

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

            $created = !is_null($event->model->contacts);
            $contacts = $event->request->input('contacts', []);

            if(!is_null($event->model->getOriginal('deleted_at'))){
                return;
            }

            if ($created) {
                $event->model->contacts()->delete();
            }

            foreach ($contacts as $contact) {

                $contact['contact_type'] = get_class($event->model);
                $contact['contact_id'] = $event->model->getKey();
                Contact::create($contact);
            }

        } catch (\Exception $exception) {
            logglyError()->exception($exception)
                ->withRequest($event->request)
                ->performedOn(static::class)
                ->log("Error registering contact");
        }
    }
}
