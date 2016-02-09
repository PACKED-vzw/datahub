<?php
/**
 * Created by PhpStorm.
 * User: pieter
 * Date: 9/02/16
 * Time: 09:31
 */

namespace app\Services;

use Carbon\Carbon;
use App\Services\PackageManager;
use Doctrine\CouchDB\HTTP\HTTPException;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Arr;


class StorageManager
{
    protected $client;
    public function __construct()
    {
        $this->client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'datahub'));
        /*
         * Create database
         */
        try {
            $this->client->createDatabase($this->client->getDatabase());
        } catch(HTTPException $e) {
            /* Already exists - TODO improve */
        }
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
        if($package['uuid'] !== null) {
            $c_package = $this->client->putDocument($package->toArray(), $package['uuid']);
        } else {
            $c_package = $this->client->postDocument($package->toArray());
        }
        $cdb_package = [
            'id' => $c_package[0],
            'rev' => $c_package[1]
        ];
        return $cdb_package;
    }

    public function read($cdb_id)
    {
        $c_package = $this->client->findDocument($cdb_id);
        if($c_package->status != 200) {
            /*
             * This item does not exist
             */
        }
        return $c_package;
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

    public function compare_hash(ArrayCollection $package, $cdb_result)
    {
        if($package['metadata']['hash'] === $cdb_result->body['metadata']['hash']) {
            return true;
        }
        return false;
    }


}