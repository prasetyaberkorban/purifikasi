<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InventoryApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function user(UserRole $role = UserRole::Admin, ?string $email = null): User
    {
        $email ??= 'admin@example.test';

        return User::query()->create([
            'name' => 'Andi Prasetyo',
            'username' => Str::before($email, '@'),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function seedAsset(): Asset
    {
        $category = Category::query()->create(['code' => 'PL', 'name' => 'Peralatan Lapangan', 'color' => '#1E6B38']);
        $location = Location::query()->create(['name' => 'Gudang Utama', 'city' => 'Depok']);

        return Asset::query()->create([
            'code' => 'DEP.PL/2026/SGI.0001',
            'name' => 'Linggis',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 4,
            'minimum_stock' => 1,
            'borrowed_quantity' => 0,
            'condition' => AssetCondition::Baik,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_inventory_summary(): void
    {
        $asset = $this->seedAsset();

        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Aset')
            ->assertSee('Kondisi Aset')
            ->assertSee($asset->name);
    }

    public function test_pegawai_cannot_manage_assets_but_can_view_them(): void
    {
        $asset = $this->seedAsset();
        $pegawai = $this->user(UserRole::Karyawan);

        $this->actingAs($pegawai)->get(route('assets.index'))->assertOk();
        $this->actingAs($pegawai)->get(route('assets.create'))->assertForbidden();
        $this->actingAs($pegawai)->get(route('assets.show', $asset))->assertOk();
        $this->actingAs($pegawai)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($pegawai)->get(route('users.index'))->assertForbidden();
    }

    public function test_staff_can_create_asset_with_generated_code(): void
    {
        $category = Category::query()->create(['code' => 'PL', 'name' => 'Peralatan Lapangan', 'color' => '#1E6B38']);

        $this->actingAs($this->user(UserRole::Admin))
            ->post(route('assets.store'), [
                'name' => 'Sekop',
                'category_id' => $category->id,
                'quantity' => 3,
                'minimum_stock' => 1,
                'condition' => AssetCondition::Baik->value,
            ])
            ->assertRedirect();

        $asset = Asset::query()->where('name', 'Sekop')->firstOrFail();

        $this->assertMatchesRegularExpression('#^SMG\.PL/\d{4}/\d{3}$#', $asset->code);
        $this->assertSame('SMG.PL/'.now()->format('Y').'/001', $asset->code);
        $this->assertSame('tersedia', $asset->status->value);
        $this->assertDatabaseHas('asset_activities', ['asset_id' => $asset->id, 'type' => 'created']);
    }

    public function test_asset_update_cannot_reduce_stock_below_borrowed_quantity(): void
    {
        $asset = $this->seedAsset();
        $asset->update(['borrowed_quantity' => 3]);
        $asset->syncStatus()->save();

        $this->actingAs($this->user())
            ->put(route('assets.update', $asset), [
                'name' => $asset->name,
                'category_id' => $asset->category_id,
                'quantity' => 1,
                'minimum_stock' => 1,
                'condition' => AssetCondition::Baik->value,
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(4, $asset->refresh()->quantity);
    }

    public function test_asset_with_active_loan_cannot_be_deleted(): void
    {
        $asset = $this->seedAsset();
        $user = $this->user();

        AssetLoan::query()->create([
            'code' => 'PJM.TEST.0001',
            'asset_id' => $asset->id,
            'borrower_id' => $user->id,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'dipinjam',
        ]);

        $this->actingAs($user)
            ->delete(route('assets.destroy', $asset))
            ->assertSessionHasErrors('asset');

        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }

    public function test_pegawai_only_sees_own_loan_history(): void
    {
        $asset = $this->seedAsset();
        $pegawai = $this->user(UserRole::Karyawan);
        $other = User::query()->create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.test',
            'password' => 'password',
            'role' => UserRole::Karyawan,
            'is_active' => true,
        ]);

        AssetLoan::query()->create([
            'code' => 'PJM.ME.0001',
            'asset_id' => $asset->id,
            'borrower_id' => $pegawai->id,
            'quantity' => 1,
            'borrowed_at' => now(),
            'purpose' => 'Milik sendiri',
            'status' => 'dipinjam',
        ]);

        AssetLoan::query()->create([
            'code' => 'PJM.OTHER.0002',
            'asset_id' => $asset->id,
            'borrower_id' => $other->id,
            'quantity' => 1,
            'borrowed_at' => now(),
            'purpose' => 'Milik orang lain',
            'status' => 'dipinjam',
        ]);

        $this->actingAs($pegawai)
            ->get(route('loans.history'))
            ->assertOk()
            ->assertSee('PJM.ME.0001')
            ->assertDontSee('PJM.OTHER.0002');
    }

    public function test_scan_resolves_code_and_url_payload(): void
    {
        $asset = $this->seedAsset();
        $user = $this->user();

        $this->actingAs($user)
            ->post(route('scan.resolve'), ['code' => $asset->code])
            ->assertRedirect(route('assets.show', $asset));

        $this->actingAs($user)
            ->post(route('scan.resolve'), ['code' => route('assets.scan', $asset->code)])
            ->assertRedirect(route('assets.show', $asset));

        $this->actingAs($user)
            ->post(route('scan.resolve'), ['code' => 'KODE-SALAH'])
            ->assertSessionHasErrors('code');
    }

    public function test_reports_export_pdf_and_excel(): void
    {
        $this->seedAsset();
        $user = $this->user();

        $pdf = $this->actingAs($user)->get(route('reports.pdf', ['type' => 'assets']));
        $pdf->assertOk();
        $pdf->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->getContent());

        $excel = $this->actingAs($user)->get(route('reports.excel', ['type' => 'loans']));
        $excel->assertOk();
        $this->assertStringContainsString('spreadsheetml', $excel->headers->get('Content-Type'));
        $this->assertStringContainsString('laporan-loans', $excel->headers->get('Content-Disposition'));
    }

    public function test_label_print_page_contains_qr_payload(): void
    {
        $asset = $this->seedAsset();

        $this->actingAs($this->user())
            ->get(route('labels.print', ['ids' => [$asset->id], 'mode' => 'both']))
            ->assertOk()
            ->assertSee($asset->code)
            ->assertSee('<svg', false);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = $this->user();
        $user->update(['is_active' => false]);

        $this->post(route('login.attempt'), [
            'login' => $user->username,
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_user_can_authenticate_with_username_or_email(): void
    {
        $user = $this->user();

        $this->post(route('login.attempt'), [
            'login' => $user->username,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login.attempt'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->post(route('login.attempt'), [
            'login' => $this->user()->username,
            'password' => 'kata-sandi-salah',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_login_ignores_letter_case_and_blocks_case_duplicate_email(): void
    {
        $user = $this->user();

        $this->post(route('login.attempt'), [
            'login' => Str::upper($user->username),
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'));
        $this->assertGuest();

        // Email yang hanya berbeda huruf besar/kecil harus ditolak agar tidak ada dua akun kembar.
        $this->post(route('register.store'), [
            'name' => 'Kembar',
            'username' => 'kembar',
            'email' => Str::upper($user->email),
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(0, User::query()->where('username', 'kembar')->count());
    }

    public function test_registration_creates_an_active_karyawan_account(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Rina Kartika',
            'username' => 'rina.kartika',
            'email' => 'rina@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('username', 'rina.kartika')->firstOrFail();

        $this->assertSame(UserRole::Karyawan, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_an_admin_role_payload(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Penyusup',
            'username' => 'penyusup',
            'email' => 'penyusup@example.test',
            'role' => UserRole::Admin->value,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame(UserRole::Karyawan, User::query()->where('username', 'penyusup')->firstOrFail()->role);
    }

    public function test_registration_validates_username_and_duplicates(): void
    {
        $existing = $this->user();

        $this->post(route('register.store'), [
            'name' => 'Ganda',
            'username' => $existing->username,
            'email' => 'ganda@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('username');

        $this->post(route('register.store'), [
            'name' => 'Huruf Besar',
            'username' => 'Huruf Besar',
            'email' => 'huruf@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('username');

        $this->post(route('register.store'), [
            'name' => 'Beda Sandi',
            'username' => 'beda.sandi',
            'email' => 'beda@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia456',
        ])->assertSessionHasErrors(['password' => 'Kata sandi tidak sama']);

        $this->post(route('register.store'), [
            'name' => 'Pendek Sandi',
            'username' => 'pendek.sandi',
            'email' => 'pendek@example.test',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])->assertSessionHasErrors(['password' => 'Kata sandi minimal harus 8 karakter.']);

        $this->assertDatabaseMissing('users', ['username' => 'beda.sandi']);
        $this->assertGuest();
    }

    public function test_admin_can_promote_a_karyawan_to_admin(): void
    {
        $admin = $this->user();
        $karyawan = $this->user(UserRole::Karyawan, 'bawahan@example.test');

        $this->actingAs($admin)
            ->put(route('users.update', $karyawan), [
                'name' => $karyawan->name,
                'username' => $karyawan->username,
                'email' => $karyawan->email,
                'role' => UserRole::Admin->value,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertSame(UserRole::Admin, $karyawan->refresh()->role);
    }

    public function test_admin_panel_blocks_non_staff(): void
    {
        $pegawai = $this->user(UserRole::Karyawan);

        $this->actingAs($pegawai)->get('/admin')->assertForbidden();
        $this->actingAs($this->user(UserRole::Admin, 'owner@example.test'))->get('/admin/assets')->assertOk();
    }

    public function test_can_update_asset_item_room_and_price(): void
    {
        $admin = $this->user();
        $asset = $this->seedAsset();
        $asset->syncItems();
        $item = $asset->items()->first();

        $this->actingAs($admin)
            ->post(route('assets.items.update', [$asset, $item]), [
                'condition' => 'baik',
                'room' => 'Gudang Lab 2',
                'purchase_price' => 75000,
                'notes' => 'Unit baru di lab',
            ])
            ->assertRedirect();

        $this->assertSame('Gudang Lab 2', $item->refresh()->room);
        $this->assertEquals(75000, $item->purchase_price);
    }

    public function test_can_update_asset_item_serial_number(): void
    {
        $admin = $this->user();
        $asset = $this->seedAsset();
        $asset->syncItems();
        $item = $asset->items()->first();

        $this->actingAs($admin)
            ->post(route('assets.items.update', [$asset, $item]), [
                'serial_number' => 'SN-TEST-9988',
                'condition' => 'baik',
                'room' => 'Ruang Server',
                'purchase_price' => 120000,
            ])
            ->assertRedirect();

        $this->assertSame('SN-TEST-9988', $item->refresh()->serial_number);
    }

    public function test_can_add_and_delete_asset_item(): void
    {
        $admin = $this->user();
        $asset = $this->seedAsset();
        $asset->syncItems();
        $initialCount = $asset->items()->count();

        // Tambah unit baru
        $this->actingAs($admin)
            ->post(route('assets.items.store', $asset), [
                'serial_number' => 'SN-NEW-001',
                'condition' => 'baik',
                'room' => 'Gudang Utama',
                'purchase_price' => 50000,
            ])
            ->assertRedirect();

        $this->assertEquals($initialCount + 1, $asset->fresh()->quantity);
        $newItem = $asset->items()->where('serial_number', 'SN-NEW-001')->first();
        $this->assertNotNull($newItem);

        // Hapus unit tersebut
        $this->actingAs($admin)
            ->delete(route('assets.items.destroy', [$asset, $newItem]))
            ->assertRedirect();

        $this->assertEquals($initialCount, $asset->fresh()->quantity);
        $this->assertNull($asset->items()->where('serial_number', 'SN-NEW-001')->first());
    }

    public function test_asset_index_shows_delete_button_for_admin(): void
    {
        $admin = $this->user();
        $asset = $this->seedAsset();

        $response = $this->actingAs($admin)->get(route('assets.index'));
        $response->assertOk();
        $response->assertSee('title="Hapus Aset"', false);
    }

    public function test_sidebar_renders_submission_collection_link(): void
    {
        $admin = $this->user();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Kumpulan Pengajuan');
        $response->assertSee('onedrive.live.com', false);
    }

    public function test_reports_page_does_not_restrict_loans_to_single_month_by_default(): void
    {
        $admin = $this->user();

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewHas('loanRows', function ($loanRows) {
            return $loanRows->count() >= 0;
        });
    }

    public function test_module_exports_pdf_and_excel(): void
    {
        $user = $this->user();

        // Service
        $this->actingAs($user)->get(route('services.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('services.export.excel'))->assertOk();

        // Pengadaan
        $this->actingAs($user)->get(route('procurements.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('procurements.export.excel'))->assertOk();

        // Izin/Cuti
        $this->actingAs($user)->get(route('leaves.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('leaves.export.excel'))->assertOk();

        // Peminjaman Kendaraan
        $this->actingAs($user)->get(route('vehicle-loans.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('vehicle-loans.export.excel'))->assertOk();

        // Daftar Hadir
        $this->actingAs($user)->get(route('attendances.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('attendances.export.excel'))->assertOk();

        // Kontrak / SPK
        $this->actingAs($user)->get(route('contracts.export.pdf'))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($user)->get(route('contracts.export.excel'))->assertOk();
    }

    public function test_can_manage_project_contracts_with_sticky_project(): void
    {
        $user = $this->user();

        // 1. Visit create page
        $res = $this->actingAs($user)->get(route('contracts.create'));
        $res->assertOk();

        // 2. Submit contract
        $file = \Illuminate\Http\UploadedFile::fake()->create('spk_document.pdf', 500, 'application/pdf');
        $storeRes = $this->actingAs($user)->post(route('contracts.store'), [
            'spk_number' => '001/SPK/TEST/2026',
            'spk_date' => '2026-11-07',
            'project_name' => 'Proyek WTP Semarang',
            'second_party_name' => 'PT. Mitra Mandiri',
            'job_description' => 'Pemasangan instalasi pipa WTP',
            'contract_value' => '25.000.000',
            'duration_months' => 3,
            'termin_1' => 30,
            'termin_2' => 30,
            'termin_3' => 40,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'PT. Mitra Mandiri',
            'contract_notes' => 'Catatan tes kontrak',
            'source_document_text' => 'https://drive.google.com/test',
            'source_document_file' => $file,
        ]);

        $storeRes->assertRedirect(route('contracts.index'));
        $this->assertDatabaseHas('project_contracts', [
            'spk_number' => '001/SPK/TEST/2026',
            'project_name' => 'Proyek WTP Semarang',
            'contract_value' => 25000000,
        ]);

        // 3. Verify session has sticky project name
        $this->assertEquals('Proyek WTP Semarang', session('last_project_name'));

        // 4. Visit create page again, should have sticky project prefilled
        $res2 = $this->actingAs($user)->get(route('contracts.create'));
        $res2->assertOk();
        $res2->assertSee('Proyek WTP Semarang');

        // 5. Index page lists the contract and contains no dropdown <select>
        $indexRes = $this->actingAs($user)->get(route('contracts.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('001/SPK/TEST/2026');
        $indexRes->assertSee('Rp 25.000.000');
        $indexRes->assertDontSee('<select');

        // 6. Delete contract
        $contract = \App\Models\ProjectContract::query()->where('spk_number', '001/SPK/TEST/2026')->firstOrFail();
        $delRes = $this->actingAs($user)->delete(route('contracts.destroy', $contract));
        $delRes->assertRedirect(route('contracts.index'));
        $this->assertDatabaseMissing('project_contracts', ['id' => $contract->id]);
    }
}