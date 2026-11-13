<?php

namespace Database\Seeders;

use App\Models\Kas;
use App\Models\DetailKas;
use Illuminate\Database\Seeder;

class CashDenominationSeeder extends Seeder
{
    public function run(): void
    {
        Kas::where('status', 'cash')->each(function (Kas $kas) {
            $existingDenominations = DetailKas::where('id_kas_warung', $kas->id)
                ->pluck('pecahan')
                ->map(fn ($pecahan) => (int) $pecahan)
                ->all();

            $missingDenominations = array_diff(DetailKas::DENOMINATIONS, $existingDenominations);
            foreach ($missingDenominations as $pecahan) {
                DetailKas::create([
                    'id_kas_warung' => $kas->id,
                    'pecahan' => (string) $pecahan,
                    'jumlah' => 0,
                ]);
            }
        });
    }
}
