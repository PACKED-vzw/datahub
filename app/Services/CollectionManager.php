<?php

namespace App\Services;

use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\CouchDBClient;

class CollectionManager
{
    protected $client;

    public function __construct()
    {
        $this->client = CouchDBClient::create(array('dbname' => 'collections'));

        // Create a database
        try {
            $this->client->createDatabase($this->client->getDatabase());
        } catch(HTTPException $e) {
            /* Already exists - TODO improve */
        }
    }

    public function all()
    {
        $response = $this->client->allDocs();
        $collections = $response->body['rows'];
        return $collections;
    }

    public function create($value)
    {
        return $this->client->postDocument([
            'slug' => $value['slug'],
            'name' => $value['name'],
            'records' => $value['records']
        ]);
    }

    public function update($key, $value)
    {
        $response = $this->client->findDocument($key);
        $document = $response->body;

        return $this->client->putDocument([
            'slug' => $value['slug'],
            'name' => $value['name'],
            'records' => $value['records']
        ], $document['_id'], $document['_rev']);
    }

    public function delete($key)
    {
        $response = $this->client->findDocument($key);
        $document = $response->body;
        $this->client->deleteDocument($document['_id'], $document['_rev']);
    }

    public function read($key)
    {
        $response = $this->client->findDocument($key);
        return $response->body;
    }
}

