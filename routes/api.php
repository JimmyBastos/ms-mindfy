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
            $content = DB::transaction(function () use ($generationService, $request) {
                $contentData = $generationService->searchContent([
                    'title' => $request->title
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

                    if (!$creator->creators()->find($creator->id)) {
                        $content->creators()->attach($creator->id);
                    }
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
            if (!$content->blocks()->exists()) {
                $contentOutlineData = $generationService->generateContentOutline(
                    $content->load(['creators', 'categories', 'blocks'])->toArray()
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

        Route::post('contents/{content}/cover', function (Content $content, GenerationService $generationService) {
            $contentCover = $generationService->generateContentCover(
                $content->load(['creators'])->toArray()
            );

            $image = file_get_contents($contentCover->image->url);

            $path = "/contents/$content->id/cover/" . md5($content->id) . "-" . now()->toTimeString() . ".jpg";;

            Storage::disk()->put($path, $image, 'public');

            $content->cover()->update(['priority' => 1]);;

            $content->cover()->create([
                'tag'         => 'cover',
                'type'        => 'image',
                'name'        => [],
                'description' => [],
                'priority'    => 0,
                'content'     => ["url" => Storage::temporaryUrl($path, now()->addDay())],
            ]);

            return $content->load(['cover']);
        });

        Route::post('contents/{content}/speech', function (Content $content, GenerationService $generationService) {
            $content->load(['creators', 'categories', 'blocks']);

            $content->blocks()->each(function ($block) use ($content, $generationService) {
                $blockContent = $block->content;

                if (!isset($blockContent['audio'])) {
                    $audio = $generationService->generateAudioSpeech([
                        'lang' => 'pt-BR',
                        'text' => $blockContent['text']['pt']
                    ]);

                    $path = "/contents/$content->id/block/$block->id/pt-BR/" . md5($block->id) . ".aac";

                    Storage::disk()->put($path, $audio, ['visibility' => 'public']);

                    $blockContent['audio']['pt'] = Storage::url($path, now()->addDays(3));

                    $block->content = $blockContent;

                    $block->save();
                }
            });

            return $content->load(['blocks']);
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
