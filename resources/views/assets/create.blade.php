<x-app-layout title="Tambah Barang" subtitle="Daftarkan aset baru ke inventaris kantor" active="assets">
    <x-asset-form :asset="$asset" :categories="$categories" :locations="$locations"
                  :preset-category="$presetCategory" :preset-location="$presetLocation"
                  :action="route('assets.store')" submit-label="Simpan Aset" />
</x-app-layout>