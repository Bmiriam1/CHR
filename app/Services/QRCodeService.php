<?php

namespace App\Services;

use App\Models\Host;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate QR code data for a host.
     */
    public function generateQRCodeData(Host $host): array
    {
        return [
            'type' => 'attendance_host',
            'host_id' => $host->id,
            'host_code' => $host->code,
            'program_id' => $host->program_id,
            'company_id' => $host->company_id,
            'latitude' => $host->latitude,
            'longitude' => $host->longitude,
            'radius' => $host->radius_meters,
            'generated_at' => now()->toISOString(),
            'expires_at' => now()->addYear()->toISOString(),
        ];
    }

    /**
     * Generate QR code string for a host.
     */
    public function generateQRCodeString(Host $host): string
    {
        return 'HOST_' . $host->code . '_' . now()->format('YmdHis');
    }

    /**
     * Generate QR code as SVG using real QR code library.
     */
    public function generateQRCodeSVG(Host $host, int $size = 200): string
    {
        $qrData = $this->generateQRCodeData($host);
        $qrString = $this->generateQRCodeString($host);

        return QrCode::size($size)
            ->format('svg')
            ->generate($qrString);
    }

    /**
     * Generate a simple QR pattern from hash.
     */
    private function generateQRPattern(string $hash, int $size): array
    {
        $pattern = [];
        $moduleSize = max(1, floor($size / 25)); // 25x25 grid
        $gridSize = 25;

        for ($i = 0; $i < $gridSize; $i++) {
            $pattern[$i] = [];
            for ($j = 0; $j < $gridSize; $j++) {
                $index = ($i * $gridSize + $j) % strlen($hash);
                $pattern[$i][$j] = (ord($hash[$index]) % 2) === 0;
            }
        }

        return $pattern;
    }

    /**
     * Create SVG from pattern.
     */
    private function createSVG(array $pattern, int $size, string $text): string
    {
        $moduleSize = max(1, floor($size / 25));
        $svg = '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="white"/>';

        foreach ($pattern as $row => $modules) {
            foreach ($modules as $col => $isBlack) {
                if ($isBlack) {
                    $x = $col * $moduleSize;
                    $y = $row * $moduleSize;
                    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $moduleSize . '" height="' . $moduleSize . '" fill="black"/>';
                }
            }
        }

        $svg .= '<text x="10" y="' . ($size - 10) . '" font-family="monospace" font-size="8" fill="black">' . htmlspecialchars($text) . '</text>';
        $svg .= '</svg>';

        return $svg;
    }

    /**
     * Generate QR code as PNG using real QR code library.
     */
    public function generateQRCodePNG(Host $host, int $size = 200): string
    {
        $qrString = $this->generateQRCodeString($host);

        return QrCode::size($size)
            ->format('png')
            ->generate($qrString);
    }

    /**
     * Validate QR code string.
     */
    public function validateQRCodeString(string $qrCode): bool
    {
        return preg_match('/^HOST_[A-Z0-9]+_\d{14}$/', $qrCode);
    }

    /**
     * Parse QR code string to extract host code.
     */
    public function parseQRCodeString(string $qrCode): ?string
    {
        if (!$this->validateQRCodeString($qrCode)) {
            return null;
        }

        $parts = explode('_', $qrCode);
        return $parts[1] ?? null;
    }

    /**
     * Generate QR code for multiple hosts.
     */
    public function generateBulkQRCodes(array $hosts): array
    {
        $results = [];

        foreach ($hosts as $host) {
            $results[] = [
                'host' => $host,
                'qr_code' => $this->generateQRCodeString($host),
                'qr_data' => $this->generateQRCodeData($host),
                'svg' => $this->generateQRCodeSVG($host),
            ];
        }

        return $results;
    }

    /**
     * Generate QR code with custom data.
     */
    public function generateCustomQRCode(array $data, string $prefix = 'CUSTOM'): string
    {
        $json = json_encode($data);
        $hash = substr(md5($json), 0, 8);
        return $prefix . '_' . $hash . '_' . now()->format('YmdHis');
    }
}
