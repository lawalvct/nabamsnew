<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

/**
 * Defensive handling for every user/admin file upload.
 *
 * - The real content type (finfo magic bytes) must match an allow-listed extension.
 * - Images are decoded and re-encoded with GD, which strips EXIF/GPS metadata and any
 *   payload appended to or hidden inside the file (polyglots).
 * - PDFs must start with a PDF header and may not carry JavaScript or launch actions.
 * - Office files may not contain macros/ActiveX, and archives are checked for zip bombs.
 * - Files are always stored under a random name with our own extension, never the client's.
 */
class SecureUpload
{
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public const EVIDENCE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

    public const RESOURCE_EXTENSIONS = ['pdf', 'docx', 'xlsx', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'webp', 'mp3', 'mp4'];

    /**
     * Extension => content types finfo may report for a genuine file of that type.
     */
    private const TYPES = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
        'txt' => ['text/plain'],
        'csv' => ['text/plain', 'text/csv', 'application/csv'],
        'mp3' => ['audio/mpeg', 'audio/mp3'],
        'mp4' => ['video/mp4'],
    ];

    private const MAX_IMAGE_PIXELS = 30_000_000;

    private const MAX_ZIP_ENTRIES = 5000;

    private const MAX_ZIP_UNCOMPRESSED_BYTES = 500 * 1024 * 1024;

    /**
     * Validate and store an image, re-encoded and optionally scaled down. Returns the stored path.
     */
    public static function storeImage(UploadedFile $file, string $field, string $directory, string $disk, int $maxDimension = 1600): string
    {
        $extension = self::inspect($file, $field, self::IMAGE_EXTENSIONS);

        return self::putContents(self::reencodeImage($file, $field, $extension, $maxDimension), $directory, $disk, $extension);
    }

    /**
     * Validate and store any allow-listed file. Returns path, extension, content type and size.
     *
     * @param  array<int, string>  $allowedExtensions
     * @return array{path: string, extension: string, mime: string, size: int}
     */
    public static function store(UploadedFile $file, string $field, string $directory, string $disk, array $allowedExtensions, int $maxImageDimension = 2400): array
    {
        $extension = self::inspect($file, $field, $allowedExtensions);

        if (in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            $contents = self::reencodeImage($file, $field, $extension, $maxImageDimension);
            $path = self::putContents($contents, $directory, $disk, $extension);

            return ['path' => $path, 'extension' => $extension, 'mime' => self::mimeFor($extension), 'size' => strlen($contents)];
        }

        $name = Str::random(40).'.'.$extension;
        $path = Storage::disk($disk)->putFileAs($directory, $file, $name);

        if (! $path) {
            throw ValidationException::withMessages([$field => 'The file could not be saved. Please try again.']);
        }

        return ['path' => $path, 'extension' => $extension, 'mime' => self::mimeFor($extension), 'size' => (int) $file->getSize()];
    }

    /**
     * Headers for sending a stored upload back to a browser.
     *
     * @return array<string, string>
     */
    public static function responseHeaders(string $extension, bool $inline = true): array
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Content-Type' => self::mimeFor($extension),
        ];

        // Browsers' built-in PDF viewers refuse to render inside a sandboxed CSP, so only other types get it.
        if (! $inline || $extension !== 'pdf') {
            $headers['Content-Security-Policy'] = "default-src 'none'; img-src 'self'; media-src 'self'; style-src 'unsafe-inline'; sandbox";
        }

        return $headers;
    }

    public static function mimeFor(string $extension): string
    {
        return self::TYPES[strtolower($extension)][0] ?? 'application/octet-stream';
    }

    public static function safeDownloadName(string $title, string $extension): string
    {
        $base = Str::slug(Str::limit($title, 80, '')) ?: 'nabams-resource';

        return $base.'.'.strtolower($extension);
    }

    /**
     * @param  array<int, string>  $allowedExtensions
     */
    private static function inspect(UploadedFile $file, string $field, array $allowedExtensions): string
    {
        $fail = fn (string $message) => throw ValidationException::withMessages([$field => $message]);

        if (! $file->isValid()) {
            $fail('The upload failed. Please try again.');
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;

        if (! in_array($extension, $allowedExtensions, true) || ! isset(self::TYPES[$extension])) {
            $fail('This file type is not allowed. Allowed types: '.strtoupper(implode(', ', array_unique(array_map(fn ($ext) => $ext === 'jpeg' ? 'jpg' : $ext, $allowedExtensions)))).'.');
        }

        // An unreadable file (e.g. quarantined by antivirus) is rejected rather than crashing the request.
        $realPath = $file->getRealPath();
        $detected = $realPath && is_readable($realPath) ? @(new \finfo(FILEINFO_MIME_TYPE))->file($realPath) : false;

        if ($detected === false) {
            $fail('This file could not be read. It may be damaged or blocked by a virus scanner.');
        }

        if (! in_array($detected, self::TYPES[$extension], true)) {
            $fail('The file content does not match its extension.');
        }

        match (true) {
            in_array($extension, ['docx', 'xlsx', 'pptx'], true) => self::inspectOfficeDocument($file, $fail),
            $extension === 'pdf' => self::inspectPdf($file, $fail),
            in_array($extension, ['txt', 'csv'], true) => self::inspectText($file, $fail),
            default => null,
        };

        return $extension;
    }

    private static function inspectPdf(UploadedFile $file, \Closure $fail): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        $header = $handle ? fread($handle, 1024) : '';
        $handle && fclose($handle);

        if (! str_contains((string) $header, '%PDF-')) {
            $fail('This is not a valid PDF file.');
        }

        // Decode #xx escapes in PDF names (e.g. /J#61vaScript) before looking for active content.
        $contents = preg_replace_callback('/#([0-9A-Fa-f]{2})/', fn ($m) => chr(hexdec($m[1])), (string) file_get_contents($file->getRealPath()));

        if (preg_match('#/(JavaScript|JS|Launch|EmbeddedFile|RichMedia)\b#', $contents)) {
            $fail('PDFs with scripts, embedded files or launch actions are not allowed. Please save/print it as a plain PDF and try again.');
        }
    }

    private static function inspectOfficeDocument(UploadedFile $file, \Closure $fail): void
    {
        $zip = new ZipArchive();

        if ($zip->open($file->getRealPath(), ZipArchive::RDONLY) !== true) {
            $fail('This document could not be read. Please re-save it and try again.');
        }

        try {
            if ($zip->locateName('[Content_Types].xml') === false) {
                $fail('This is not a valid Office document.');
            }

            if ($zip->numFiles > self::MAX_ZIP_ENTRIES) {
                $fail('This document contains too many parts.');
            }

            $totalSize = 0;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = strtolower((string) ($stat['name'] ?? ''));
                $totalSize += (int) ($stat['size'] ?? 0);

                if (str_contains($name, '..') || str_starts_with($name, '/')) {
                    $fail('This document contains unsafe paths.');
                }

                if (str_contains($name, 'vbaproject') || str_contains($name, 'activex') || (str_ends_with($name, '.bin') && ! str_contains($name, 'printersettings'))) {
                    $fail('Documents containing macros or ActiveX controls are not allowed.');
                }
            }

            if ($totalSize > self::MAX_ZIP_UNCOMPRESSED_BYTES) {
                $fail('This document expands to an unsafe size.');
            }
        } finally {
            $zip->close();
        }
    }

    private static function inspectText(UploadedFile $file, \Closure $fail): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        $sample = $handle ? fread($handle, 65536) : '';
        $handle && fclose($handle);

        if (str_contains((string) $sample, "\0")) {
            $fail('Text files must not contain binary data.');
        }
    }

    private static function reencodeImage(UploadedFile $file, string $field, string $extension, int $maxDimension): string
    {
        $fail = fn (string $message) => throw ValidationException::withMessages([$field => $message]);
        $path = $file->getRealPath();
        $info = @getimagesize($path);

        if (! $info || $info[0] < 1 || $info[1] < 1) {
            $fail('This image could not be read.');
        }

        if ($info[0] * $info[1] > self::MAX_IMAGE_PIXELS) {
            $fail('This image is too large. Please use an image under 30 megapixels.');
        }

        $image = @imagecreatefromstring((string) file_get_contents($path));

        if (! $image) {
            $fail('This image could not be processed.');
        }

        $image = self::applyExifOrientation($image, $path, $extension);

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxDimension / max($width, $height));

        if ($scale < 1) {
            $resized = imagescale($image, max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)), IMG_BICUBIC);
            $image = $resized;
        }

        ob_start();

        match ($extension) {
            'png' => (function () use ($image) {
                imagesavealpha($image, true);
                imagepng($image, null, 6);
            })(),
            'webp' => imagewebp($image, null, 85),
            default => imagejpeg($image, null, 85),
        };

        $contents = (string) ob_get_clean();

        if ($contents === '') {
            $fail('This image could not be processed.');
        }

        return $contents;
    }

    private static function applyExifOrientation(\GdImage $image, string $path, string $extension): \GdImage
    {
        if ($extension !== 'jpg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        return $rotated ?: $image;
    }

    private static function putContents(string $contents, string $directory, string $disk, string $extension): string
    {
        $path = trim($directory, '/').'/'.Str::random(40).'.'.$extension;

        Storage::disk($disk)->put($path, $contents);

        return $path;
    }
}
