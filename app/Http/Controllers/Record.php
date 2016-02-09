<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\PackageManager;
use App\Services\StorageManager;

class Record extends Controller
{

    protected $packageManager;
    protected $storageManager;

    public function __construct(PackageManager $packageManager, StorageManager $storageManager)
    {
        $this->packageManager = $packageManager;
        $this->storageManager = $storageManager;
    }

    function index(Request $request)
    {
        // 1. validatie van request
        $lidoRecord = $request->getContent();

        // 2. XML naar JSON (naief)
        // $lido_json = $this->packageManager->XMLToJson('');
        $package = $this->packageManager->package('');
        $cdb_control = [
           'id' => '0a103d69',
           'rev' => '1-b68362c84085e31c4571821fbe488c3d'
           ];
        $package->set('cdb_control', $cdb_control);

        // 3. UUID toekennen

        // 4. datum + instelling

        // 5. check als reeds in couch
        // The uuid is in fact a hash of a hash. This is also the ID. If we have an ID-collision, check
        // whether the longer hashes match. If they do, it is the same. If they don't, we have a problem.
        $o_package = $package;
        $e_cdb_package = $this->storageManager->read($package['uuid']);
        if($e_cdb_package->status === 200) {
            /* This one exists */
            if($this->storageManager->compare_hash($package, $e_cdb_package) === true) {
                /* It's the same */
                $o_package = $e_cdb_package->body;
                $o_package['cdb_control'] = [
                    'id' => $o_package['_id'],
                    'rev' => $o_package['_rev']
                ];
            } else {
                throw new Exception('ID collision.');
            }
        } else {
            /* This one doesn't TODO only do this for 404 errors */
            $cdb_info = $this->storageManager->create($package);
            // 6. Update cdb_control
            $o_package['cdb_control'] = [
                'id' => $cdb_info['id'],
                'rev' => $cdb_info['rev']
            ];
        }

        // 7. Return resultaat

        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode((array)$o_package), 200, $headers);
    }
}
