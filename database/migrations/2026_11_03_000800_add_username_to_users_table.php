<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 64)->nullable()->unique()->after('name');
        });

        // Akun lama diberi username dari bagian depan email agar tetap bisa masuk.
        $taken = DB::table('users')
            ->whereNotNull('username')
            ->pluck('username')
            ->map(fn ($username) => Str::lower((string) $username))
            ->all();

        foreach (DB::table('users')->select('id', 'email')->whereNull('username')->orderBy('id')->get() as $user) {
            $base = preg_replace('/[^a-z0-9._-]/', '', Str::lower(Str::before((string) $user->email, '@'))) ?: 'user'.$user->id;
            $candidate = $base;

            for ($suffix = 2; in_array($candidate, $taken, true); $suffix++) {
                $candidate = $base.$suffix;
            }

            $taken[] = $candidate;

            DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
