<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Content;
use App\Models\Creator;
use App\Services\GenerationService;
use App\Utils\Helpers;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateContentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $title)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(GenerationService $generationService): void
    {
        DB::transaction(function () use ($generationService) {
            $contentData = $generationService->generateContent([
                'title' => $this->title
            ]);

            /** @var Content $content */
            $content = Content::firstOrCreate(
                ['slug' => Helpers::slugify($contentData['title']['en'])],
                $contentData
            );

            foreach ($contentData['creators'] as $creatorData) {
                $creator = Creator::firstOrCreate(
                    ['slug' => Helpers::slugify($creatorData['name'])],
                    $creatorData
                );

                $content->creators()->attach($creator->id);
            }

            foreach ($contentData['categories'] as $categoryData) {
                $category = Category::firstWhere('slug', Helpers::slugify($categoryData['name']['en']));

                if ($category) {
                    $content->categories()->attach($category->id);
                }
            }
        });
    }
}
