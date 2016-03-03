<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\CollectionManager;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Collection extends Controller
{
    protected $collectionManager;

    public function __construct(CollectionManager $collectionManager)
    {
        $this->collectionManager = $collectionManager;
    }

    public function index()
    {
        $response = $this->collectionManager->all();

        $collections = [
            'links' => [
                'self' => [
                    'href' => url('/collections')
                ]
            ],
            'embedded' => [
                'collections' => array_map(function ($collection) {
                    return [
                        'name' => $collection['doc']['name'],
                        'slug' => $collection['doc']['slug'],
                        'links' => [
                            'self' => [
                                'href' => url('/collection/'.$collection['id'])
                            ]
                        ]
                    ];
                }, $response)
            ]
        ];

        $headers = [
            "Access-Control-Allow-Origin" => '*',
            'Content-type' => 'application/json'
        ];
        return response(json_encode($collections), 200, $headers);
    }

    public function postCollection(Request $request)
    {
        $data = $request->getContent();

        $collection = json_decode($data, TRUE);
        $response = $this->collectionManager->create($collection);

        $collection['id'] = $response[0];
        $collection['rev'] = $response[1];
        $collection['links'] = [
            'self' => [
                'href' => url('/collection/'.$collection['id'])
            ]
        ];

        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode($collection), 200, $headers);
    }

    public function updateCollection($id, Request $request)
    {
        $data = $request->getContent();
        $collection = json_decode($data, TRUE);

        $response = $this->collectionManager->update($id, $collection);

        $collection['id'] = $response[0];
        $collection['rev'] = $response[1];
        $collection['links'] = [
            'self' => [
                'href' => url('/collection/'.$collection['id'])
            ]
        ];

        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode($collection), 200, $headers);
    }

    public function getCollection($id)
    {
        $response = $this->collectionManager->read($id);

        $collection = [
            'id' => $response['_id'],
            'slug' => $response['slug'],
            'name' => $response['name'],
            'links' => [
                'self' => [
                    'href' => url('/collection/'.$response['_id']),
                ]
            ],
            'embedded'=> [
                'records' => array_map(function ($record) {
                    return [
                        'links' => [
                            'self' => [
                                'href' => url('/record/' . $record),
                            ],
                        ]
                    ];
                }, $response['records'])
            ]
        ];

        $headers = [
            "Access-Control-Allow-Origin" => '*',
            'Content-type' => 'application/json'
        ];
        return response(json_encode($collection), 200, $headers);
    }

    public function deleteCollection($id)
    {
        $this->collectionManager->delete($id);
        $headers = [
            'Content-type' => 'application/json'
        ];
        return response(json_encode(['status' => 'deleted']), 200, $headers);
    }
}
