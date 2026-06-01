<?php

namespace App\Services;

use App\Enums\EducationalLevel;
use App\Models\Student;
use App\Support\QrCodePng;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use RuntimeException;

class StudentIdCardService
{
    public function renderFront(Student $student): InterventionImage
    {
        $templateKey = $this->resolveTemplateKey($student);
        $level = $this->levelConfig($templateKey);
        $layout = $level['front'] ?? [];
        $img = $this->loadTemplate($templateKey, 'front');

        if (! empty($level['accent_bar'])) {
            $this->drawAccentBar($img, $level['accent'] ?? '#333333', $layout['accent_bar'] ?? []);
        }

        if (! empty($level['show_level_label'])) {
            $levelText = $layout['level'] ?? [];
            $this->drawTextFromLayout(
                $img,
                $level['label'] ?? strtoupper(str_replace('_', ' ', $templateKey)),
                $levelText,
                $level['accent'] ?? '#000000'
            );
        }

        $fullName = strtoupper(trim($student->firstname.' '.$student->lastname));
        $this->drawTextFromLayout($img, $fullName, $layout['name'] ?? []);

        $programLine = $this->programLine($student);
        if ($programLine !== '' && isset($layout['program_line'])) {
            $this->drawTextFromLayout($img, strtoupper($programLine), $layout['program_line']);
        }

        if ($student->student_id && isset($layout['student_id'])) {
            $this->drawTextFromLayout($img, $student->student_id, $layout['student_id']);
        }

        if ($student->qrcode) {
            $barcodeCfg = $layout['barcode'] ?? [];
            $scale = (int) ($barcodeCfg['scale'] ?? 8);
            $barHeight = (int) ($barcodeCfg['height'] ?? 300);
            $barcode = DNS1D::getBarcodePNG($student->qrcode, 'C128', $scale, $barHeight);
            $barcodeImage = Image::make(base64_decode($barcode));
            $img->insert(
                $barcodeImage,
                'top-left',
                (int) ($barcodeCfg['x'] ?? 0),
                (int) ($barcodeCfg['y'] ?? 0)
            );

            if (isset($layout['qrcode_label'])) {
                $this->drawTextFromLayout($img, $student->qrcode, $layout['qrcode_label']);
            }
        }

        $this->insertProfilePhoto($img, $student, $level['photo'] ?? null);

        return $img;
    }

    public function renderBack(Student $student): InterventionImage
    {
        $templateKey = $this->resolveTemplateKey($student);
        $layout = $this->levelConfig($templateKey)['back'] ?? [];
        $img = $this->loadTemplate($templateKey, 'back');

        if ($student->birth_date && isset($layout['birth_date'])) {
            $formattedDate = Carbon::parse($student->birth_date)->format('m-d-Y');
            $this->drawTextFromLayout($img, $formattedDate, $layout['birth_date']);
        }

        if ($student->blood_type && isset($layout['blood_type'])) {
            $this->drawTextFromLayout($img, $student->blood_type, $layout['blood_type']);
        }

        if ($student->emergency_person && isset($layout['emergency_name'])) {
            $this->drawTextFromLayout($img, $student->emergency_person, $layout['emergency_name']);
        }

        if ($student->emergency_relationship && isset($layout['emergency_relationship'])) {
            $this->drawTextFromLayout($img, $student->emergency_relationship, $layout['emergency_relationship']);
        }

        if ($student->emergency_number && isset($layout['emergency_number'])) {
            $this->drawTextFromLayout($img, $student->emergency_number, $layout['emergency_number']);
        }

        if ($student->emergency_address && isset($layout['emergency_address'])) {
            $this->drawWrappedAddress($img, $student->emergency_address, $layout['emergency_address']);
        }

        if ($student->student_signature && file_exists(base_path($student->student_signature))) {
            $sig = $layout['signature'] ?? [];
            $signature = Image::make(base_path($student->student_signature))->resize(
                (int) ($sig['width'] ?? 2000),
                (int) ($sig['height'] ?? 1000)
            );
            $img->insert(
                $signature,
                'top-left',
                (int) ($sig['x'] ?? 50),
                (int) ($sig['y'] ?? 2875)
            );
        }

        if ($student->qrcode && isset($layout['qr'])) {
            $qrCfg = $layout['qr'];
            $qrPng = QrCodePng::generate($student->qrcode, (int) ($qrCfg['size'] ?? 1300), 0);
            $qrImage = Image::make((string) $qrPng);
            $img->insert($qrImage, 'top-left', (int) ($qrCfg['x'] ?? 0), (int) ($qrCfg['y'] ?? 0));
        }

        return $img;
    }

    public function resolveTemplateKey(Student $student): string
    {
        $level = $student->educational_level;

        if ($level instanceof EducationalLevel) {
            return $level->idCardTemplateKey();
        }

        return EducationalLevel::idCardTemplateKeyFor(
            is_string($level) ? $level : $student->getRawOriginal('educational_level')
        );
    }

    public function templatePath(string $templateKey, string $side): ?string
    {
        $candidates = [
            base_path("images/id_templates/{$templateKey}/{$side}.png"),
            base_path("images/id_templates/college/{$side}.png"),
            base_path("images/id_templates/{$side}.png"),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    public function programLine(Student $student): string
    {
        $parts = array_filter([
            $student->year,
            $student->course,
        ]);

        return implode(' · ', $parts);
    }

    /** @return array<string, mixed> */
    private function levelConfig(string $templateKey): array
    {
        $config = config("id_cards.student_levels.{$templateKey}");

        if (! is_array($config) || $config === []) {
            throw new RuntimeException("Missing ID card layout config for level [{$templateKey}] in config/id_cards.php");
        }

        return $config;
    }

    private function loadTemplate(string $templateKey, string $side): InterventionImage
    {
        $path = $this->templatePath($templateKey, $side);

        if ($path === null) {
            throw new RuntimeException(
                "Missing ID template: images/id_templates/{$templateKey}/{$side}.png ".
                '(run php artisan id-cards:init-student-templates if you have legacy front.png/back.png)'
            );
        }

        return Image::make($path);
    }

    private function drawAccentBar(InterventionImage $img, string $color, array $bar): void
    {
        $width = (int) ($bar['width'] ?? $img->width());
        $height = (int) ($bar['height'] ?? 120);
        $x = (int) ($bar['x'] ?? 0);
        $y = (int) ($bar['y'] ?? 0);

        $strip = Image::canvas($width, $height, $color);
        $img->insert($strip, 'top-left', $x, $y);
    }

    /** @param array<string, int>|null $photo */
    private function insertProfilePhoto(InterventionImage $img, Student $student, ?array $photo): void
    {
        if ($photo === null) {
            return;
        }

        if (! $student->profile_picture || ! file_exists(base_path($student->profile_picture))) {
            return;
        }

        $profile = Image::make(base_path($student->profile_picture))->resize(
            (int) ($photo['width'] ?? 290),
            (int) ($photo['height'] ?? 290)
        );

        $img->insert($profile, 'top-left', (int) ($photo['x'] ?? 0), (int) ($photo['y'] ?? 0));
    }

    private function drawTextFromLayout(
        InterventionImage $img,
        string $text,
        array $layout,
        string $defaultColor = '#000'
    ): void {
        if ($layout === []) {
            return;
        }

        $this->drawText(
            $img,
            $text,
            (int) ($layout['x'] ?? 0),
            (int) ($layout['y'] ?? 0),
            (int) ($layout['size'] ?? 24),
            $layout['color'] ?? $defaultColor,
            $layout['align'] ?? 'center',
            $layout['valign'] ?? 'top'
        );
    }

    private function drawWrappedAddress(InterventionImage $img, string $address, array $layout): void
    {
        $address = strtoupper($address);
        $maxChars = (int) ($layout['max_chars'] ?? 60);
        $words = explode(' ', $address);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if (strlen($current.' '.$word) <= $maxChars) {
                $current .= ($current ? ' ' : '').$word;
            } else {
                $lines[] = $current;
                $current = $word;
            }
        }
        if ($current) {
            $lines[] = $current;
        }

        if ($lines === []) {
            return;
        }

        $maxLength = max(array_map('strlen', $lines));
        $fontSize = (int) ($layout['size'] ?? 250);

        if ($maxLength > 25 && $maxLength <= 35) {
            $fontSize = (int) round($fontSize * 0.8);
        } elseif ($maxLength > 35 && $maxLength <= 45) {
            $fontSize = (int) round($fontSize * 0.6);
        } elseif ($maxLength > 45) {
            $fontSize = (int) round($fontSize * 0.4);
        }

        $centerX = (int) ($layout['x'] ?? 0);
        $startY = (int) ($layout['y'] ?? 0);
        $spacing = $fontSize + 10;

        foreach ($lines as $i => $line) {
            $this->drawText(
                $img,
                $line,
                $centerX,
                $startY + ($i * $spacing),
                $fontSize,
                $layout['color'] ?? '#000',
                $layout['align'] ?? 'center'
            );
        }
    }

    private function drawText(
        InterventionImage $img,
        string $text,
        int $x,
        int $y,
        int $size,
        string $color = '#000',
        string $align = 'center',
        string $valign = 'top'
    ): void {
        $fontPathBold = public_path('fonts/arialbd.ttf');
        $fontPathRegular = public_path('fonts/arialbd.ttf');

        if (file_exists($fontPathBold)) {
            $img->text($text, $x, $y, function ($font) use ($fontPathBold, $size, $color, $align, $valign) {
                $font->file($fontPathBold);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });

            return;
        }

        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$ox, $oy]) {
            $img->text($text, $x + $ox, $y + $oy, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                $font->file($fontPathRegular);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        }

        $img->text($text, $x, $y, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
            $font->file($fontPathRegular);
            $font->size($size);
            $font->color($color);
            $font->align($align);
            $font->valign($valign);
        });
    }
}
