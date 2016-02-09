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

    public function create($package)
    {
        $c_package = $this->client->postDocument((array)$package);
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

    public function update($cdb_id, $cdb_rev, $package)
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