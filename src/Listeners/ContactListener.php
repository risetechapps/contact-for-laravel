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

            if ($event->request->has('contacts')) {
                $contacts = $event->request->input('contacts');
            }else if ($event->request->has('person.contacts')) {
                $contacts = $event->request->input('person.contacts');
            }else{
                if(!empty(\RiseTechApps\Contact\Contact::getContact())){
                    $contacts = \RiseTechApps\Contact\Contact::getContact();
                }
            }

            if(!is_null($event->model->getOriginal('deleted_at'))){
                return;
            }

            if(count($contacts) > 0){

                if ($created) {
                    $event->model->contacts()->delete();
                }

                foreach ($contacts as $contact) {

                    $contact['contact_type'] = get_class($event->model);
                    $contact['contact_id'] = $event->model->getKey();
                    Contact::create($contact);
                }
            }

        } catch (\Exception $exception) {
            logglyError()->exception($exception)
                ->withRequest($event->request)
                ->performedOn($event->model)
                ->log("Error registering contact");
        }
    }
}
