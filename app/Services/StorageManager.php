<?php

namespace App\Services;

use Carbon\Carbon;
use App\Services\PackageManager;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\CouchDB\CouchDBClient;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;

class StorageManager
{
    protected $client;

    public function __construct()
    {
        $this->client = CouchDBClient::create(array('dbname' => 'datahub'));

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

        if ($response->status != 200) {
            return false; // throw exception
        }

        $objects = new ArrayCollection();
        foreach ($response->body['rows'] as $row) {
            $package = new ArrayCollection($row['doc']);
            $objects->add($package);
        }

        $result = [
            'total_rows' => $response->body['total_rows'],
            'offset' => $response->body['offset'],
            'rows' => $objects
        ];

        return $result;
    }

    public function create(ArrayCollection $package)
    {
        /*
         * All this works, but we want to create our own ids, so we use PUT
         */
        /*
        $c_package = $this->client->postDocument($package->toArray());
        $cdb_package = [
            'id' => $c_package[0],
            'rev' => $c_package[1]
        ];
        return $cdb_package;
        */
        if ($package['uuid'] !== null) {
            $c_package = $this->client->putDocument($package->toArray(), $package['uuid']);
        } else {
            $c_package = $this->client->postDocument($package->toArray());
        }

        return $c_package;
    }

    /**
     * Reads a document based on a cbd_id
     *
     * @param $cbd_id the uuid of the document
     * @return false if the document could not be retrieved or the document.
     */
    public function read($cdb_id)
    {
        $c_package = $this->client->findDocument($cdb_id);
        if ($c_package->status != 200) {
            return false;
        }

        return new ArrayCollection($c_package->body);
    }

    public function update($cdb_id, $cdb_rev, ArrayCollection $package)
    {
        /*
         * We don't update, that's too much hassle. We remove it and recreate it.
         */
        $this->delete($cdb_id, $cdb_rev);
        return $this->create($package);
    }

    public function delete($cdb_id, $cdb_rev)
    {
        $this->client->deleteDocument($cdb_id, $cdb_rev);
    }
}
