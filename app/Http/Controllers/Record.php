<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;

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

    /**
     * POST /record
     *
     * Create or update an existing record in the datahub
     */
    function index(Request $request)
    {
        // Retrieve & validate othe request data.
        $lidoRecord = $request->getContent();
        $xml_file = __DIR__.'/lido_example.xml';
        $xml_data = file_get_contents($xml_file);

        // Package the data.
        $package = $this->packageManager->package($xml_data);

        $response = [];

        $uuid = $package->get('uuid');

        // Check if the package was already stored and needs updating.
        if ($e_cdb_package = $this->storageManager->read($uuid)) {
            if ($this->packageManager->compareHashes($e_cdb_package, $package)) {
                // Do nothing!
                $response = [
                    'status' => 'untouched',
                    'uuid' => $e_cdb_package->get('uuid')
                ];
            } else {
                // We'll transfer the UUID of the deleted package.
                $uuid = $e_cdb_package->get('uuid');
                $package->set('uuid', $uuid);

                // Delete the entire record from the store and recreate it.
                $_id = $e_cdb_package->get('_id');
                $_rev = $e_cdb_package->get('_rev');
                $this->storageManager->delete($_id, $_rev);
                $this->storageManager->create($package);

                $response = [
                    'status' => 'updated',
                    'uuid' => $package->get('uuid')
                ];
            }
        } else {
            $this->storageManager->create($package);
            $response = [
                'status' => 'created',
                'uuid' => $package->get('uuid')
            ];
        }

        // Output response
        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode((array)$response), 200, $headers);
    }

    /**
     * GET /record/{id}
     *
     * Fetches a single record and displays it as application/xml.
     */
    public function record($uuid)
    {
        if ($package = $this->storageManager->read($uuid)) {
            // Extract the lido record from the package.
            $record = $package->get('record');

            // Conver the JSON object to XML.
            $xml = $this->packageManager->objToXML($record);

            // Output the XML as application/xml
            $headers = [
                'Content-type' => 'application/xml'
            ];
            return response($xml, 200, $headers);
        } else {
            App::abort(404);
        }
    }
}
