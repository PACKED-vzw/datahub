<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\PackageManager;
use App\Services\StorageManager;
use App\Services\SearchManager;
use Sabre\Xml\Service;

class Record extends Controller
{
    protected $packageManager;

    protected $storageManager;

    public function __construct(PackageManager $packageManager, StorageManager $storageManager, SearchManager $searchManager)
    {
        $this->packageManager = $packageManager;
        $this->storageManager = $storageManager;
        $this->searchManager = $searchManager;
        $this->service = new Service();
    }

    /**
     * POST /record
     *
     * Create or update an existing record in the datahub
     */
    function store(Request $request)
    {
        // Retrieve & validate othe request data.
        $lidoRecord = $request->getContent();
        //$xml_file = __DIR__.'/lido_example.xml';
        //$lidoRecord = file_get_contents($xml_file);

        // Package the data.
        $package = $this->packageManager->package($lidoRecord);

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
            "Access-Control-Allow-Origin" => '*',
            'Content-type' => 'application/json'
        ];
        return response(json_encode($response), 200, $headers);
    }

    /**
     * GET /record/{id}
     *
     * Fetches a single record and displays it as application/xml.
     */
    public function record($uuid)
    {
        if (strstr($uuid, '.')) {
            list($uuid, $format) = explode('.', $uuid);
        } else {
            $format = 'json';
        }

        if ($record = $this->searchManager->retrieve($uuid)) {
            if ($format == 'xml') {
                // Convert the JSON object to XML.
                $data = $this->packageManager->objToXML($record);

                // Output the XML as application/xml
                $headers = [
                    "Access-Control-Allow-Origin" => '*',
                    'Content-type' => 'application/xml'
                ];
            }

            if ($format == 'json') {
                $data = json_encode([
                    'links' => [
                        'self' => [
                            'href' => url('record/'.$uuid)
                        ]
                    ],
                    'record' => $record,
                ]);

                // Output the XML as application/json
                $headers = [
                    "Access-Control-Allow-Origin" => '*',
                    'Content-type' => 'application/json'
                ];
            }

            return response($data, 200, $headers);
        } else {
            App::abort(404);
        }
    }

    /**
     * GET /collection/{facet}/{term}
     *
     * Fetches a list of records based on a single search axis. Supports 3 types
     * of axes: materials, creator, institute.
     */
    public function collection($facet, $term)
    {
        $result = $this->searchManager->search($facet, $term);

        // *very* leaky abstraction: XML writing / reading should reside in a
        // separate service.
        //
        // @todo
        //  Specify the format / datamodel / structure of the 'collection' API
        //  call. The REST principle dictates to at least provides links to the
        //  individual resources usable for further querying.
        $urls = array_map(function ($hit) {
            return ['name' => 'url', 'value' => url('record', $hit)];
        }, $result['hits']);


        $xml = $this->service->write('result', [
            'facet' => [
                'value' => $term
            ],
            'total' => [
                'value' => $result['total']
            ],
            'urls' => [
                'value' => $urls
            ]
        ]);

        // Output the XML as application/xml
        $headers = [
            "Access-Control-Allow-Origin" => '*',
            'Content-type' => 'application/xml'
        ];
        return response($xml, 200, $headers);
    }
}
