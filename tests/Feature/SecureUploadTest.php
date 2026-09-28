<?php

namespace Tests\Feature;

use App\Support\SecureUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use ZipArchive;

class SecureUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_php_script_renamed_to_image_is_rejected(): void
    {
        $this->assertRejected(UploadedFile::fake()->createWithContent('photo.jpg', '<?php system($_GET["c"]); ?>'), SecureUpload::IMAGE_EXTENSIONS);
    }

    public function test_html_renamed_to_pdf_is_rejected(): void
    {
        $this->assertRejected(UploadedFile::fake()->createWithContent('receipt.pdf', '<html><script>alert(1)</script></html>'), SecureUpload::EVIDENCE_EXTENSIONS);
    }

    public function test_disallowed_extensions_are_rejected(): void
    {
        $this->assertRejected(UploadedFile::fake()->createWithContent('payload.exe', 'MZ'), SecureUpload::RESOURCE_EXTENSIONS);
        $this->assertRejected(UploadedFile::fake()->createWithContent('image.svg', '<svg onload="alert(1)"/>'), SecureUpload::RESOURCE_EXTENSIONS);
    }

    public function test_pdf_with_javascript_is_rejected_even_when_obfuscated(): void
    {
        $this->assertRejected(UploadedFile::fake()->createWithContent('a.pdf', "%PDF-1.7\n1 0 obj << /OpenAction << /S /JavaScript /JS (app.alert(1)) >> >> endobj"), ['pdf']);
        $this->assertRejected(UploadedFile::fake()->createWithContent('b.pdf', "%PDF-1.7\n1 0 obj << /OpenAction << /S /J#61vaScript >> >> endobj"), ['pdf']);
        $this->assertRejected(UploadedFile::fake()->createWithContent('c.pdf', "%PDF-1.7\n1 0 obj << /S /Launch /F (cmd.exe) >> endobj"), ['pdf']);
    }

    public function test_clean_pdf_is_accepted_and_stored_with_random_name(): void
    {
        $stored = SecureUpload::store(
            UploadedFile::fake()->createWithContent('../../evil name.pdf', "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\n%%EOF"),
            'file', 'uploads', 'local', ['pdf'],
        );

        $this->assertMatchesRegularExpression('#^uploads/[A-Za-z0-9]{40}\.pdf$#', $stored['path']);
        Storage::disk('local')->assertExists($stored['path']);
    }

    public function test_images_are_reencoded_which_strips_hidden_payloads(): void
    {
        $image = UploadedFile::fake()->image('photo.png', 50, 50);
        file_put_contents($image->getRealPath(), '<?php echo "pwned"; ?>', FILE_APPEND);

        $path = SecureUpload::storeImage($image, 'photo', 'photos', 'local', 800);
        $contents = Storage::disk('local')->get($path);

        $this->assertStringNotContainsString('<?php', $contents);
        $this->assertNotFalse(@imagecreatefromstring($contents));
    }

    public function test_large_images_are_scaled_down(): void
    {
        $path = SecureUpload::storeImage(UploadedFile::fake()->image('big.jpg', 3000, 1500), 'photo', 'photos', 'local', 800);

        [$width, $height] = getimagesizefromstring(Storage::disk('local')->get($path));

        $this->assertSame(800, $width);
        $this->assertSame(400, $height);
    }

    public function test_office_documents_with_macros_are_rejected(): void
    {
        $clean = $this->makeDocx();
        $withMacro = $this->makeDocx(['word/vbaProject.bin' => 'macro']);

        $stored = SecureUpload::store($clean, 'file', 'docs', 'local', ['docx']);
        $this->assertSame('docx', $stored['extension']);

        $this->assertRejected($withMacro, ['docx']);
    }

    public function test_text_files_with_binary_content_are_rejected(): void
    {
        $this->assertRejected(UploadedFile::fake()->createWithContent('notes.txt', "hello\0\x01\x02binary"), ['txt']);
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private function assertRejected(UploadedFile $file, array $allowed): void
    {
        try {
            SecureUpload::store($file, 'file', 'uploads', 'local', $allowed);
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('file', $exception->errors());
            $this->assertSame([], Storage::disk('local')->allFiles('uploads'), 'Rejected files must not be stored.');

            return;
        }

        $this->fail("{$file->getClientOriginalName()} should have been rejected.");
    }

    /**
     * @param  array<string, string>  $extraEntries
     */
    private function makeDocx(array $extraEntries = []): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>');
        $zip->addFromString('word/document.xml', '<?xml version="1.0"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body/></w:document>');

        foreach ($extraEntries as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

        $zip->close();

        return new UploadedFile($path, 'document.docx', null, null, true);
    }
}
