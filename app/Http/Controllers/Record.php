<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\PackageManager;

class Record extends Controller
{

	protected $packageManager;

		public function __construct(PackageManager $packageManager) {
			$this->packageManager = $packageManager;
		}

		function index(Request $request) {
			// 1. validatie van request
			$lidoRecord = $request->getContent();

			// 2. XML naar JSON (naief)
			// $lido_json = $this->packageManager->XMLToJson('');
			$lido_json = $this->packageManager->package('');

			// 3. UUID toekennen

			// 4. datum + instelling

			// 5. check als reeds in couch

			// 6. schrijf naar couch

			// 7. Return resultaat

			$headers = [
				'Content-type' => 'application/json'
			];
			return response($lido_json, 200, $headers);
		}
}
