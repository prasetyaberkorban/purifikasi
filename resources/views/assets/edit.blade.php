<x-app-layout title="Ubah Aset" :subtitle="$asset->name.' · '.$asset->code" active="assets">
    <x-asset-form :asset="$asset" :categories="$categories" :locations="$locations"
                  :action="route('assets.update', $asset)" method="PUT" submit-label="Simpan Perubahan" />
</x-app-layout>