<?php

namespace RiseTechApps\Contact;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RiseTechApps\Contact\Skeleton\SkeletonClass
 */
class ContactFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'contact';
    }
}
