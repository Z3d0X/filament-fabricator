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

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            $afterUrls = $sortUrls(FilamentFabricator::getPageUrls());

            expect($afterUrls)->not->toEqual($beforeUrls);

            $pageUrls = $sortUrls($page->getAllUrls());

            expect($afterUrls)->toEqual($pageUrls);
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

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = $sortUrls($allUrls);

            $fromPages = $sortUrls([
                ...$page->getAllUrls(),
                ...$child->getAllUrls(),
            ]);

            $expectedUrls = $sortUrls([
                '/my-slug',
                '/my-slug/my-stuff',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);

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
            $allUrls = $sortUrls($allUrls);

            $fromPages = $sortUrls([
                ...$page->getAllUrls(),
                ...$child->getAllUrls(),
                ...$descendant->getAllUrls(),
            ]);

            $expectedUrls = $sortUrls([
                '/my-slug',
                '/my-slug/my-stuff',
                '/my-slug/my-stuff/abc-xyz',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);
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

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            $expected = $sortUrls(['/not-my-slug']);

            $newUrls = $sortUrls($page->getAllUrls());

            $allUrls = $sortUrls(FilamentFabricator::getPageUrls());

            expect($allUrls)->toEqual($expected);
            expect($newUrls)->toEqual($expected);
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

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            /**
             * @var Page[] $descendants
             */
            $descendants = [$child1, $child2, $child3, $childOfChild];
            $pages = [$page, ...$descendants];
            $oldUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $page->slug = 'not-my-slug';
            $page->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $newUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            expect($newUrlSets)->not->toEqual($oldUrlSets);

            $allUrls = $sortUrls(FilamentFabricator::getPageUrls());

            $fromPages = $sortUrls(collect($pages)->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            $expectedUrls = $sortUrls([
                '/not-my-slug',
                '/not-my-slug/child-1',
                '/not-my-slug/child-2',
                '/not-my-slug/child-3',
                '/not-my-slug/child-2/subchild-1',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);

            $child2->slug = 'not-child-2-xyz';
            $child2->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $allUrls = $sortUrls(FilamentFabricator::getPageUrls());
            $fromPages = $sortUrls(collect($pages)->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            $expectedUrls = $sortUrls([
                '/not-my-slug',
                '/not-my-slug/child-1',
                '/not-my-slug/not-child-2-xyz',
                '/not-my-slug/child-3',
                '/not-my-slug/not-child-2-xyz/subchild-1',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);
        });

        it('properly updates itself and descendants when changing which page is the parent (BelongsTo#associate and BelongsTo#dissociate)', function () {
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
             * @var Page $child1
             */
            $child1 = Page::create([
                'title' => 'My child 1',
                'slug' => 'child-1',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child2
             */
            $child2 = Page::create([
                'title' => 'My child 2',
                'slug' => 'child-2',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child3
             */
            $child3 = Page::create([
                'title' => 'My child 3',
                'slug' => 'child-3',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $childOfChild
             */
            $childOfChild = Page::create([
                'title' => 'Subchild 1',
                'slug' => 'subchild-1',
                'blocks' => [],
                'parent_id' => $child2->id,
            ]);

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            /**
             * @var Page[] $descendants
             */
            $descendants = [$child1, $child2, $child3, $childOfChild];
            $pages = [$page, ...$descendants];
            $oldUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $child2->parent()->associate($child1);
            $child2->save();

            $child3->parent()->dissociate();
            $child3->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $newUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $fromPages = $sortUrls(collect($pages)->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            expect($newUrlSets)->not->toEqual($oldUrlSets);

            $allUrls = $sortUrls(FilamentFabricator::getPageUrls());

            $expectedUrls = $sortUrls([
                '/my-slug',
                '/my-slug/child-1',
                '/my-slug/child-1/child-2',
                '/child-3',
                '/my-slug/child-1/child-2/subchild-1',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);
        });

        it('properly updates itself and descendants when changing which page is the parent (Model#update)', function () {
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
             * @var Page $child1
             */
            $child1 = Page::create([
                'title' => 'My child 1',
                'slug' => 'child-1',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child2
             */
            $child2 = Page::create([
                'title' => 'My child 2',
                'slug' => 'child-2',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child3
             */
            $child3 = Page::create([
                'title' => 'My child 3',
                'slug' => 'child-3',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $childOfChild
             */
            $childOfChild = Page::create([
                'title' => 'Subchild 1',
                'slug' => 'subchild-1',
                'blocks' => [],
                'parent_id' => $child2->id,
            ]);

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            /**
             * @var Page[] $descendants
             */
            $descendants = [$child1, $child2, $child3, $childOfChild];
            $pages = [$page, ...$descendants];
            $oldUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $child2->update([
                'parent_id' => $child1->id,
            ]);

            $child3->update([
                'parent_id' => null,
            ]);

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $newUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $fromPages = $sortUrls(collect($pages)->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            expect($newUrlSets)->not->toEqual($oldUrlSets);

            $allUrls = $sortUrls(FilamentFabricator::getPageUrls());

            $expectedUrls = $sortUrls([
                '/my-slug',
                '/my-slug/child-1',
                '/my-slug/child-1/child-2',
                '/child-3',
                '/my-slug/child-1/child-2/subchild-1',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);
        });

        it('properly updates itself and descendants when changing which page is the parent (manual change and Model#save)', function () {
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
             * @var Page $child1
             */
            $child1 = Page::create([
                'title' => 'My child 1',
                'slug' => 'child-1',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child2
             */
            $child2 = Page::create([
                'title' => 'My child 2',
                'slug' => 'child-2',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $child3
             */
            $child3 = Page::create([
                'title' => 'My child 3',
                'slug' => 'child-3',
                'blocks' => [],
                'parent_id' => $page->id,
            ]);

            /**
             * @var Page $childOfChild
             */
            $childOfChild = Page::create([
                'title' => 'Subchild 1',
                'slug' => 'subchild-1',
                'blocks' => [],
                'parent_id' => $child2->id,
            ]);

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            $descendants = [$child1, $child2, $child3, $childOfChild];
            $pages = [$page, ...$descendants];
            $oldUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $child2->parent_id = $child1->id;
            $child2->save();

            $child3->parent_id = null;
            $child3->save();

            foreach ($descendants as $descendant) {
                $descendant->refresh();
            }

            $newUrlSets = array_map(fn (Page $page) => $page->getAllUrls(), $descendants);

            $fromPages = $sortUrls(collect($pages)->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            expect($newUrlSets)->not->toEqual($oldUrlSets);

            $allUrls = FilamentFabricator::getPageUrls();
            $allUrls = $sortUrls($allUrls);

            $expectedUrls = $sortUrls([
                '/my-slug',
                '/my-slug/child-1',
                '/my-slug/child-1/child-2',
                '/child-3',
                '/my-slug/child-1/child-2/subchild-1',
            ]);

            expect($allUrls)->toEqual($expectedUrls);
            expect($fromPages)->toEqual($expectedUrls);
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

            /**
             * @var Page $child
             */
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

            $expected = ['/my-child-page'];

            expect($urls)->toEqual($expected);
            expect($child->getAllUrls())->toEqual($expected);
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

            $sortUrls = fn (array $urls) => collect($urls)
                ->sort()
                ->values()
                ->toArray();

            $child->delete();
            $descendant->refresh();
            $page->refresh();

            expect($descendant->parent_id)->toBe($page->id);

            $urls = $sortUrls(FilamentFabricator::getPageUrls());

            $expected = $sortUrls([
                '/my-slug',
                '/my-slug/my-sub-page',
            ]);

            $fromPages = $sortUrls(collect([$page, $descendant])->flatMap(fn (Page $page) => $page->getAllUrls())->toArray());

            expect($urls)->toEqual($expected);
            expect($fromPages)->toEqual($expected);
        });
    });
});
