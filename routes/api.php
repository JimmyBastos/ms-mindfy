<?php

use App\Models\Category;
use App\Models\Content;
use App\Models\Creator;
use App\Services\GenerationService;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

set_time_limit(0);

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::group(['prefix' => 'generation'], function () {
        Route::post('contents', function (Request $request, GenerationService $generationService) {
            $content = DB::transaction(function () use ($generationService) {
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

                return $content;
            });

            return $content->load(['categories', 'creators']);
        });

        Route::post('contents/{content}/expand', function (Content $content, GenerationService $generationService) {
            $content->load(['creators', 'categories', 'blocks']);

            if (!$content->blocks()->exists()) {
                $contentOutlineData = $generationService->generateContentOutline(
                    $content->toArray()
                );

                $content->blocks()->createMany(
                    $contentOutlineData->map(fn ($block, $priority) => [
                        'tag'         => 'block',
                        'type'        => 'audio',
                        'name'        => $block['name'],
                        'description' => $block['description'],
                        'content'     => Arr::only($block['content'], ['text']),
                        'priority'    => $priority
                    ])
                );
            }

            return $content->refresh()->load(['creators', 'categories', 'blocks']);
        });


        Route::post('contents/{content}/speech', function (Content $content, GenerationService $generationService) {
            $content->load(['creators', 'categories', 'blocks']);


            $block = $content->blocks()->first();

            $generationService->generateAudioSpeech([
                'lang' => 'pt-BR',
                'text' => $block->content->text->pt
            ]);



            return $content->refresh()->load(['creators', 'categories', 'blocks']);
        });

        Route::post('contents/{content}/expand-old', function (Content $content, GenerationService $generationService) {
            $content->load(['creators', 'categories', 'blocks']);

            if ($content->blocks()->exists()) {
                $expandedContentData = $generationService->expandContentOutline(
                    $content->toArray()
                );

                $content->blocks()->each(function ($block, $index) use ($expandedContentData) {
                    if (empty($block->content)) {
                        $expandedContent = $expandedContentData[$index] ?? null;

                        if ($expandedContent) {
                            $block->content = Arr::only($expandedContent, ['text']);
                            $block->save();
                        }
                    }
                });

                return $content->refresh()->load(['creators', 'categories', 'blocks']);
            }

            return [];
        });
    });

    Route::get('contents/{content}', function (Content $content) {
        return $content->load(['cover', 'creators', 'categories', 'preview', 'blocks', 'attachments']);
    });
});
