<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Setting;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorSVG;

class LabelService
{
    /** Payload QR: URL detail aset agar bisa dibuka langsung dari kamera HP. */
    public function payload(Asset $asset): string
    {
        return route('assets.scan', $asset->code);
    }

    public function qrSvg(Asset $asset): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
            'quietzoneSize' => 2,
            'scale' => 8,
        ]);

        return (new QRCode($options))->render($this->payload($asset));
    }

    public function qrDataUri(Asset $asset): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($this->qrSvg($asset));
    }

    public function barcodeSvg(Asset $asset, float $widthFactor = 1.6, int $height = 44): string
    {
        $generator = new BarcodeGeneratorSVG;

        return $generator->getBarcode($this->barcodeValue($asset), $generator::TYPE_CODE_128, $widthFactor, $height);
    }

    /** Kode aset dipakai sebagai isi barcode agar mudah dibaca mesin kasir/scanner. */
    public function barcodeValue(Asset $asset): string
    {
        return $asset->code;
    }

    /** @return array<string, string> */
    public function labelMeta(Asset $asset): array
    {
        return [
            'company' => (string) Setting::get('company_name', 'PT. Purifikasi'),
            'unit' => (string) Setting::get('company_unit', 'OFFICE DATABASE KANTOR'),
            'footer' => (string) Setting::get('label_footer', 'SGI - Sistem Inventaris Kantor Purifikasi'),
            'category' => $asset->category?->name ?? '-',
            'location' => $asset->location?->label ?? '-',
            'status' => $asset->status->label(),
        ];
    }
}