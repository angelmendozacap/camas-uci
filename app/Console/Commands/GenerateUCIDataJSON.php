<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateUCIDataJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:json-uci-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a json file with UCI beds data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $URL = env('OPEN_COVID_URL', 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQjDUQorXKZF9xsM6iR5Vl6oEc14uBj0g73arQdVAoQiWsppdFeaHpM0exftRlpCBGo591KVrFra_eF/pub?gid=2018912534&single=true&chrome=false&output=csv');

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

        $uciBedsJSON =  json_encode($csvArr, JSON_PRETTY_PRINT);

        Storage::disk('public')->put('data/uci_beds.json', $uciBedsJSON);

        $this->info('JSON file generated "' . asset('storage/data/uci_beds.json').'"');

        return 1;
    }
}
