<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitStudentIdCardTemplates extends Command
{
    protected $signature = 'id-cards:init-student-templates';

    protected $description = 'Copy legacy student ID templates into grade_school, high_school, and college folders';

    public function handle(): int
    {
        $base = base_path('images/id_templates');
        $keys = ['grade_school', 'high_school', 'college'];
        $sides = ['front', 'back'];

        $legacyFront = "{$base}/front.png";
        $legacyBack = "{$base}/back.png";

        if (! is_file($legacyFront) && ! is_file($legacyBack)) {
            $this->error('No legacy templates found. Add images/id_templates/front.png and back.png first.');

            return self::FAILURE;
        }

        foreach ($keys as $key) {
            $dir = "{$base}/{$key}";
            File::ensureDirectoryExists($dir);

            foreach ($sides as $side) {
                $target = "{$dir}/{$side}.png";
                if (is_file($target)) {
                    $this->line("Skip (exists): {$target}");

                    continue;
                }

                $source = "{$base}/{$side}.png";
                if (! is_file($source)) {
                    $this->warn("Missing source: {$source}");

                    continue;
                }

                File::copy($source, $target);
                $this->info("Created: {$target}");
            }
        }

        $this->newLine();
        $this->info('Done. Customize each folder’s front.png / back.png for grade school, high school, and college.');

        return self::SUCCESS;
    }
}
