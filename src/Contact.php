<?php

namespace RiseTechApps\Contact;

class Contact
{
    protected static array $contact = [];

    public static function setContact(array $contact): void
    {
        static::$contact = $contact;
    }

    public static function getContact(): array
    {
        return static::$contact;
    }
}
