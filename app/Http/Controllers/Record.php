<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Record extends Controller
{
		public function __construct() {

		}

		function index(Request $request) {
			// 1. validatie van request
			$lidoRecord = $request->getContent();

			// 2. XML naar JSON (naief)


			// 3. UUID toekennen

			// 4. datum + instelling

			// 5. check als reeds in couch

			// 6. schrijf naar couch

			// 7. Return resultaat

			return "test";
		}
}
