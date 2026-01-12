<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;

class GenerateBlogSlugs extends Command
{
    protected $signature = 'blog:generate-slugs {--force : Regenerate slugs even when one already exists}';

    protected $description = 'Generate missing slugs for blog titles.';

    public function handle(): int
    {
        $force = $this->option('force');
        $updated = 0;

        Blog::chunkById(100, function ($blogs) use (&$updated, $force) {
            foreach ($blogs as $blog) {
                if (! $blog->title) {
                    continue;
                }

                if (! $force && ! empty($blog->slug)) {
                    continue;
                }

                $blog->slug = null;
                $blog->save();
                $updated++;
            }
        });

        $this->info("Slugs generated for {$updated} blog(s).");

        return self::SUCCESS;
    }
}
