<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleBlock;

class CheckArticleBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:article-blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check article blocks content structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $blocks = ArticleBlock::where('type', 'text')->get();

        $this->info("Found {$blocks->count()} text blocks in total.");

        $htmlKeyBlocks = 0;
        $directContentBlocks = 0;
        $jsonStringBlocks = 0;

        foreach ($blocks as $block) {
            $content = $block->content;

            if (is_string($content)) {
                if ($this->isJson($content)) {
                    $jsonStringBlocks++;
                    $decodedContent = json_decode($content, true);
                    if (is_array($decodedContent) && isset($decodedContent['html'])) {
                        $this->line("Block ID: {$block->id} has JSON string with 'html' key. Content: " . substr($content, 0, 100) . "...");
                    } else {
                        $this->line("Block ID: {$block->id} has JSON string without 'html' key. Content: " . substr($content, 0, 100) . "...");
                    }
                } else {
                    $directContentBlocks++;
                    $this->line("Block ID: {$block->id} has direct string content. Content: " . substr($content, 0, 100) . "...");
                }
            } else if (is_array($content)) {
                if (isset($content['html'])) {
                    $htmlKeyBlocks++;
                    $this->line("Block ID: {$block->id} has 'html' key. Content: " . substr(json_encode($content), 0, 100) . "...");
                } else {
                    $this->line("Block ID: {$block->id} is array but no 'html' key. Content: " . substr(json_encode($content), 0, 100) . "...");
                }
            } else {
                $this->line("Block ID: {$block->id} has unknown content type: " . gettype($content));
            }
        }

        $this->info("Blocks with 'html' key: {$htmlKeyBlocks}");
        $this->info("Blocks with direct content: {$directContentBlocks}");
        $this->info("Blocks with JSON string content: {$jsonStringBlocks}");
    }

    /**
     * Check if a string is valid JSON
     *
     * @param string $string
     * @return bool
     */
    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
