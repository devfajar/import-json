<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonContent = json_decode(file_get_contents('database/seeders/2019.json'), true);

        if (!isset($jsonContent['data']) || !is_array($jsonContent['data'])) {
            \Log::error("Invalid or missing 'data' key in JSON");
            return;
        }

        $originalDataCount = count($jsonContent['data']);
        \Log::info("Original data count", ['count' => $originalDataCount]);

        $indonesianMonths = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
            'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
            'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
        ];

        $processedData = array_map(function ($row) use ($indonesianMonths) {
            if (!isset($row[2]) || !is_string($row[2])) {
                \Log::info("Row skipped due to invalid date format or missing date", ['row' => $row]);
                return null;
            }

            $dateString = str_replace(array_keys($indonesianMonths), array_values($indonesianMonths), $row[2]);
            try {
                $date = Carbon::createFromFormat('d F Y', $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::info("Row skipped due to date parsing failure", ['error' => $e->getMessage(), 'row' => $row]);
                return null;
            }

            return [
                'uuid'          => uniqid(),
                'agenda_number' => $row[1],
                'date'          => $date,
                'purpose'       => $this->nullIfEmptyOrBlank($row[3]),
                'subject'       => $this->nullIfEmptyOrBlank($row[4]),
                'officer'       => 'Admin Gov',
                'department'    => $this->nullIfEmptyOrBlank($row[6]),
            ];
        }, $jsonContent['data']);

        $processedData = array_filter($processedData);

        $processedDataCount = count($processedData);
        \Log::info("Processed data count", ['count' => $processedDataCount]);

        if ($processedDataCount > 0) {
            DB::table('table_agenda_governments')->insert($processedData);
        } else {
            \Log::warning("No data to insert after processing", ['original_data_count' => $originalDataCount]);
        }
    }

    private function nullIfEmptyOrBlank($value) {
        return (is_string($value) && strtolower(trim($value)) !== 'blank' && trim($value) !== '') ? $value : null;
    }

}
