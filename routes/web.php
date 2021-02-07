<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

App::setLocale('es');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::resource('hospitals', HospitalController::class);

Route::get('/test-csv', function () {
    $URL = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQjDUQorXKZF9xsM6iR5Vl6oEc14uBj0g73arQdVAoQiWsppdFeaHpM0exftRlpCBGo591KVrFra_eF/pub?gid=2018912534&single=true&chrome=false&output=csv';

    $csvContent = file_get_contents($URL);
    $departements = [
        'AMAZONAS', 'ANCASH', 'APURIMAC', 'AREQUIPA', 'AYACUCHO', 'CAJAMARCA',
        'CALLAO', 'CUSCO', 'HUANCAVELICA', 'HUANUCO', 'ICA', 'JUNIN', 'LA LIBERTAD',
        'LAMBAYEQUE', 'LIMA', 'LORETO', 'MADRE DE DIOS', 'MOQUEGUA', 'PASCO', 'PIURA',
        'PUNO', 'SAN MARTIN', 'TACNA', 'TUMBES', 'UCAYALI',
    ];

    $rows = explode(PHP_EOL, $csvContent);
    $header = str_getcsv(array_shift($rows));
    $totalAmount = str_getcsv(array_pop($rows));

    $csvArr = [
        'date' => $header[0],
        'uci_available_nationwide' => (int) $totalAmount[1],
        'uci_total_nationwide' => (int) $totalAmount[2],
        'departaments' => [],
    ];

    $counterIdxs = -1;
    foreach ($rows as $row) {
        $rowArr = str_getcsv($row);

        if (in_array($rowArr[0], $departements)) {
            $counterIdxs++;

            $csvArr['departaments'][$counterIdxs] = [
                'department' => $rowArr[0],
                'uci_available' => (int) $rowArr[1],
                'uci_total' => (int) $rowArr[2],
                'hospitals' => [],
            ];

            continue;
        }

        array_push($csvArr['departaments'][$counterIdxs]['hospitals'], [
            'hospital' => $rowArr[0],
            'uci_available' => (int) $rowArr[1],
            'uci_total' => (int) $rowArr[2],
        ]);
    }

    $uciBedsJSON =  json_encode($csvArr);

    Storage::disk('public')->put('data/uci_beds.json', $uciBedsJSON);

    dd(asset('storage/data/uci_beds.json'));

    return $uciBedsJSON;
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
