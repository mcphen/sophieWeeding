<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateArticleDescriptionsToBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:article-descriptions-to-blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate article descriptions to article_blocks table as text blocks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $articles = \App\Models\Actualite::whereNotNull('description')->get();

        foreach ($articles as $article) {
            $article->blocks()->create([
                'type' => 'text',
                'content' => $article->description,
                'position' => 1,
            ]);
        }

        $this->info("Descriptions migrées avec succès vers la table article_blocks.");
    }
}
