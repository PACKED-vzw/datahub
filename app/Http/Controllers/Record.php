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

        // 3. UUID toekennen

        // 4. datum + instelling

        // 5. check als reeds in couch
        // Only if cdb_control.id is set
        if (array_key_exists('id', $package['cdb_control'])) {
            /*
             * This item might exist, if so, "update" it. If not
             * it is definitely something new, so just create it.
             */
            $e_package = $this->storageManager->read($package['cdb_control']['id']);
            if ($e_package->status != 200) {
                // This package does not exist, create it
                $cdb_info = $this->storageManager->create($package);
            } else {
                // This package does exist, update it
                $cdb_info = $this->storageManager->update($package['cdb_control']['id'], $package['cdb_control']['rev'], $package);
            }
        } else {
            $cdb_info = $this->storageManager->create($package);
        }


        // 6. Update cdb_control
        $package['cdb_control'] = [
            'id' => $cdb_info['id'],
            'rev' => $cdb_info['rev']
        ];

        // 7. Return resultaat

        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode((array)$package), 200, $headers);
    }
}
