<?php

namespace RiseTechApps\Contact\Support;

use Illuminate\Http\Request;

class ContactPayloadResolver
{
    /**
     * Resolve uma lista de contatos a partir de um Request ou array.
     *
     * Aceita:
     *  - Request (busca em 'contacts' ou 'person.contacts')
     *  - array com a chave 'contacts' (ou 'person.contacts')
     *  - uma lista de contatos diretamente: [['name' => ...], ['name' => ...]]
     *  - um único contato associativo: ['name' => ..., 'email' => ...]
     *
     * @return array<int, array> Lista de contatos (sempre uma lista)
     */
    public static function resolve(Request|array $data, string $key = 'contacts'): array
    {
        if ($data instanceof Request) {
            $data = $data->all();
        }

        // Chave explícita: ['contacts' => [...]]
        if (array_key_exists($key, $data) && is_array($data[$key])) {
            return array_values($data[$key]);
        }

        // Aninhado: ['person' => ['contacts' => [...]]]
        if (isset($data['person'][$key]) && is_array($data['person'][$key])) {
            return array_values($data['person'][$key]);
        }

        // Já é uma lista de contatos
        if (!empty($data) && array_is_list($data)) {
            return $data;
        }

        // Contato único associativo (precisa parecer um contato)
        $contactFields = ['name', 'telephone', 'cellphone', 'email', 'department'];
        foreach ($contactFields as $field) {
            if (array_key_exists($field, $data)) {
                return [$data];
            }
        }

        return [];
    }
}
