<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->removeDuplicateVehicleLinks();
        $this->removeDuplicateCharges();

        Schema::table('user_vehicle', function (Blueprint $table) {
            $table->unique(['user_id', 'vehicle_id'], 'user_vehicle_user_vehicle_unique');
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->unique('stay_id', 'charges_stay_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropUnique('charges_stay_id_unique');
        });

        Schema::table('user_vehicle', function (Blueprint $table) {
            $table->dropUnique('user_vehicle_user_vehicle_unique');
        });
    }

    private function removeDuplicateVehicleLinks(): void
    {
        DB::table('user_vehicle')
            ->select('user_id', 'vehicle_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'vehicle_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($row) {
                DB::table('user_vehicle')
                    ->where('user_id', $row->user_id)
                    ->where('vehicle_id', $row->vehicle_id)
                    ->where('id', '<>', $row->keep_id)
                    ->delete();
            });
    }

    private function removeDuplicateCharges(): void
    {
        DB::table('charges')
            ->select('stay_id')
            ->groupBy('stay_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('stay_id')
            ->each(function ($stayId) {
                $charges = DB::table('charges')
                    ->where('stay_id', $stayId)
                    ->orderByRaw("CASE WHEN status = 'PAID' THEN 0 ELSE 1 END")
                    ->orderBy('id')
                    ->get();

                $keep = $charges->first();
                $duplicateIds = $charges->skip(1)->pluck('id')->all();

                if (!$keep || empty($duplicateIds)) {
                    return;
                }

                DB::table('payments')
                    ->whereIn('charges_id', $duplicateIds)
                    ->update(['charges_id' => $keep->id]);

                DB::table('charges')
                    ->whereIn('id', $duplicateIds)
                    ->delete();
            });
    }
};
