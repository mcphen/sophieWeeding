<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleBlock;

class FixArticleBlocksContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:article-blocks-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix article blocks content by removing the html key from the JSON structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting to fix article blocks content...");
        $blocks = ArticleBlock::where('type', 'text')->get();
        $this->info("Found {$blocks->count()} text blocks to process.");
        $count = 0;

        foreach ($blocks as $block) {
            $content = $block->content;
            $originalContent = $content;
            $fixed = false;

            // Special case for double-encoded JSON strings (from the issue description)
            if (is_string($content)) {
                $this->line("Processing block ID: {$block->id} with string content type");

                // Exact match for the pattern in the issue description
                if (strpos($content, '{\"html\":\"<p><span style=') !== false) {
                    $this->line("Block ID: {$block->id} matches the exact pattern from the issue description");
                    try {
                        // First, try to decode as is (might be already properly JSON encoded)
                        $decodedContent = json_decode($content, true);

                        // If that fails, try with stripslashes
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $unescapedContent = stripslashes($content);
                            $decodedContent = json_decode($unescapedContent, true);
                        }

                        // If we have a valid array with html key, use it
                        if (is_array($decodedContent) && isset($decodedContent['html'])) {
                            $this->line("Block ID: {$block->id} - Successfully extracted HTML content from issue pattern");
                            $block->content = $decodedContent['html'];
                            $block->save();
                            $count++;
                            $fixed = true;
                        } else {
                            $this->line("Block ID: {$block->id} - Failed to decode issue pattern. Will try generic approaches.");
                        }
                    } catch (\Exception $e) {
                        $this->error("Error processing issue pattern for block {$block->id}: " . $e->getMessage());
                    }
                }

                // Check for content starting with {"html": (with or without escaping)
                if (!$fixed && (strpos($content, '{"html":') === 0 || strpos($content, '{\"html\":') === 0)) {
                    $this->line("Block ID: {$block->id} has content starting with {\"html\": pattern");

                    try {
                        // Try to decode the content as JSON
                        $decodedContent = json_decode($content, true);
                        if (is_array($decodedContent) && isset($decodedContent['html'])) {
                            $this->line("Block ID: {$block->id} - Successfully decoded JSON with 'html' key");
                            $block->content = $decodedContent['html'];
                            $block->save();
                            $count++;
                            $fixed = true;
                        } else {
                            $this->line("Block ID: {$block->id} - Direct JSON decode failed or no 'html' key, trying with stripslashes");
                            // If direct decoding fails, try to handle escaped JSON
                            $unescapedContent = stripslashes($content);
                            $decodedContent = json_decode($unescapedContent, true);
                            if (is_array($decodedContent) && isset($decodedContent['html'])) {
                                $this->line("Block ID: {$block->id} - Successfully decoded unescaped JSON with 'html' key");
                                $block->content = $decodedContent['html'];
                                $block->save();
                                $count++;
                                $fixed = true;
                            } else {
                                $this->line("Block ID: {$block->id} - Both decode attempts failed. Content sample: " . substr($content, 0, 100));
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Error decoding JSON for block {$block->id}: " . $e->getMessage());
                    }
                } else {
                    $this->line("Block ID: {$block->id} - Content doesn't start with {\"html\": pattern");
                }
            }

            // If not fixed by the special case, try the regular cases
            if (!$fixed) {
                // Handle case where content might be a JSON string
                if (is_string($content) && $this->isJson($content)) {
                    $this->line("Block ID: {$block->id} - Content is a valid JSON string, trying to decode");
                    $decodedContent = json_decode($content, true);
                    if (is_array($decodedContent) && isset($decodedContent['html'])) {
                        $this->line("Block ID: {$block->id} - Successfully decoded JSON string with 'html' key");
                        $block->content = $decodedContent['html'];
                        $block->save();
                        $count++;
                        continue;
                    }
                }

                // Check if content is an array and has an 'html' key
                if (is_array($content) && isset($content['html'])) {
                    $this->line("Block ID: {$block->id} - Content is an array with 'html' key");
                    // Update the content to be just the HTML value
                    $block->content = $content['html'];
                    $block->save();
                    $count++;
                }

                // Final fallback for complex JSON strings
                if (!$fixed && is_string($content) && (strpos($content, 'html') !== false)) {
                    $this->line("Block ID: {$block->id} - Trying final fallback for complex JSON string");

                    // Try various approaches to extract HTML content
                    try {
                        // Try with double decoding (for potentially double-encoded JSON)
                        $firstDecode = json_decode($content, true);
                        if (is_string($firstDecode)) {
                            $secondDecode = json_decode($firstDecode, true);
                            if (is_array($secondDecode) && isset($secondDecode['html'])) {
                                $this->line("Block ID: {$block->id} - Successfully extracted HTML with double decoding");
                                $block->content = $secondDecode['html'];
                                $block->save();
                                $count++;
                                $fixed = true;
                            }
                        }

                        // If still not fixed, try with eval (as a last resort, carefully)
                        if (!$fixed && strpos($content, '{\"html\":') === 0) {
                            $evalSafe = str_replace('$', '\$', $content); // Prevent variable interpolation
                            $evalCode = "return $evalSafe;";
                            $result = null;

                            try {
                                $result = eval($evalCode);
                                if (is_array($result) && isset($result['html'])) {
                                    $this->line("Block ID: {$block->id} - Successfully extracted HTML with eval");
                                    $block->content = $result['html'];
                                    $block->save();
                                    $count++;
                                    $fixed = true;
                                }
                            } catch (\ParseError $e) {
                                $this->error("Block ID: {$block->id} - Parse error in eval: " . $e->getMessage());
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Block ID: {$block->id} - Error in final fallback: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Fixed {$count} article blocks content.");
    }

    /**
     * Check if a string is valid JSON
     *
     * @param string $string
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
