<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Actualite;

class GenerateActualiteSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-actualite-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing actualites based on their titles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate slugs for actualites...');

        // Get all actualites that don't have a slug
        $actualites = Actualite::whereNull('slug')->orWhere('slug', '')->get();

        $count = $actualites->count();
        $this->info("Found {$count} actualites without slugs.");

        if ($count === 0) {
            $this->info('No actualites need slug generation. Exiting.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($actualites as $actualite) {
            // Generate a slug from the title
            $slug = Actualite::generateSlug($actualite->title);

            // Update the actualite with the new slug
            $actualite->slug = $slug;
            $actualite->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Slugs generated successfully!');

        return 0;
    }
}
