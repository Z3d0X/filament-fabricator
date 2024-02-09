<?php

use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Page;
use Z3d0X\FilamentFabricator\Observers\PageRoutesObserver;

describe(PageRoutesObserver::class, function () {
    beforeEach(function () {
        // Cleanup the table before every test
        Page::query()->delete();
    });

    describe('#created($page)', function () {
        it('properly adds all the page\'s URLs to the mapping', function () {
            $beforeUrls = FilamentFabricator::getPageUrls();

            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $afterUrls = FilamentFabricator::getPageUrls();

            expect($afterUrls)->not->toEqual($beforeUrls);

            $pageUrls = $page->getAllUrls();

            expect($afterUrls)->toContain(...$pageUrls);
        });

        it('properly works on child pages', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            /**
             * @var Page $page
             */
            $child = Page::create([
                'title' => 'My stuff',
                'slug' => 'my-stuff',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = collect($allUrls)
                ->sort()
                ->values()
                ->toArray();

            $expectedUrls = collect([
                '/my-slug',
                '/my-slug/my-stuff',
            ])->sort()
                ->values()
                ->toArray();

            expect($allUrls)->toEqual($expectedUrls);

            /**
             * @var Page $page
             */
            $descendant = Page::create([
                'title' => 'Abc xyz',
                'slug' => 'abc-xyz',
                'blocks' => [],
                'parent_id' => $child->id,
            ]);

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = collect($allUrls)
                ->sort()
                ->values()
                ->toArray();

            $expectedUrls = collect([
                '/my-slug',
                '/my-slug/my-stuff',
                '/my-slug/my-stuff/abc-xyz',
            ])->sort()
                ->values()
                ->toArray();

            expect($allUrls)->toEqual($expectedUrls);
        });
    });

    describe('#updated($page)', function () {
        it('removes the old URLs from the mapping', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $oldUrls = $page->getAllUrls();

            $page->slug = 'not-my-slug';
            $page->save();

            $allUrls = FilamentFabricator::getPageUrls();

            expect($allUrls)->not->toContain(...$oldUrls);
        });

        it('adds the new URLs to the mapping', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $page->slug = 'not-my-slug';
            $page->save();

            $newUrls = $page->getAllUrls();

            $allUrls = FilamentFabricator::getPageUrls();

            expect($allUrls)->toContain(...$newUrls);
        });

        it('properly updates all child (and descendant) routes', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $child1 = Page::create([
                'title' => 'My child 1',
                'slug' => 'child-1',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            $child2 = Page::create([
                'title' => 'My child 2',
                'slug' => 'child-2',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            $child3 = Page::create([
                'title' => 'My child 3',
                'slug' => 'child-3',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            $childOfChild = Page::create([
                'title' => 'Subchild 1',
                'slug' => 'subchild-1',
                'blocks' => [],
                'parent_id' => $child2->id,
            ]);

            /**
             * @var Page[] $descendants
             */
            $descendants = [$child1, $child2, $child3, $childOfChild];
            $oldUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $page->slug = 'not-my-slug';
            $page->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $newUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            expect($newUrlSets)->not->toEqual($oldUrlSets);

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = collect($allUrls)
                ->sort()
                ->values()
                ->toArray();

            $expectedUrls = collect([
                '/not-my-slug',
                '/not-my-slug/child-1',
                '/not-my-slug/child-2',
                '/not-my-slug/child-3',
                '/not-my-slug/child-2/subchild-1',
            ])->sort()
                ->values()
                ->toArray();

            expect($allUrls)->toEqual($expectedUrls);

            $child2->slug = 'not-child-2-xyz';
            $child2->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = collect($allUrls)
                ->sort()
                ->values()
                ->toArray();

            $expectedUrls = collect([
                '/not-my-slug',
                '/not-my-slug/child-1',
                '/not-my-slug/not-child-2-xyz',
                '/not-my-slug/child-3',
                '/not-my-slug/not-child-2-xyz/subchild-1',
            ])->sort()
                ->values()
                ->toArray();
        });
    });

    describe('#deleting($page)', function () {
        it('removes the page\'s URLs from the mapping', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $beforeUrls = FilamentFabricator::getPageUrls();

            $page->delete();

            $afterUrls = FilamentFabricator::getPageUrls();

            expect($afterUrls)->not->toEqual($beforeUrls);

            expect($afterUrls)->toBeEmpty();
        });

        it('sets the childrens\' parent to null if $page had no parent', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            $child = Page::create([
                'title' => 'My child page',
                'slug' => 'my-child-page',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            $page->delete();

            $child->refresh();

            expect($child->parent_id)->toBeNull();

            $urls = FilamentFabricator::getPageUrls();

            expect($urls)->toEqual(['/my-child-page']);
        });

        it('attaches the children to $page\'s parent if it had one', function () {
            /**
             * @var Page $page
             */
            $page = Page::create([
                'title' => 'My title',
                'slug' => 'my-slug',
                'blocks' => [],
                'parent_id' => null,
            ]);

            /**
             * @var Page $child
             */
            $child = Page::create([
                'title' => 'My child page',
                'slug' => 'my-child-page',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $descendant
             */
            $descendant = Page::create([
                'title' => 'My sub page',
                'slug' => 'my-sub-page',
                'blocks' => [],
                'parent_id' => $child->id,
            ]);

            $child->delete();
            $descendant->refresh();
            $page->refresh();

            expect($descendant->parent_id)->toBe($page->id);

            $urls = collect(FilamentFabricator::getPageUrls())
                ->sort()
                ->values()
                ->toArray();

            $expected = collect([
                '/my-slug',
                '/my-slug/my-sub-page',
            ])
                ->sort()
                ->values()
                ->toArray();

            expect($urls)->toEqual($expected);
        });
    });
});
