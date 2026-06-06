<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('charges', 'user_id')) {
            Schema::table('charges', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            });
        }

        DB::table('charges')
            ->join('stays', 'stays.id', '=', 'charges.stay_id')
            ->join('user_vehicle', 'user_vehicle.vehicle_id', '=', 'stays.vehicle_id')
            ->whereNull('charges.user_id')
            ->select('charges.id as charge_id', 'user_vehicle.user_id')
            ->orderBy('charges.id')
            ->get()
            ->each(function ($row) {
                DB::table('charges')
                    ->where('id', $row->charge_id)
                    ->whereNull('user_id')
                    ->update(['user_id' => $row->user_id]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('charges', 'user_id')) {
            Schema::table('charges', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
