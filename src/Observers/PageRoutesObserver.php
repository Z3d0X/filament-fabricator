<?php

namespace Z3d0X\FilamentFabricator\Observers;

use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

class PageRoutesObserver
{
    public function __construct(
        protected PageRoutesService $pageRoutesService
    ) {
    }

    /**
     * Handle the Page "created" event.
     */
    public function created(PageContract $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(PageContract $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "deleting" event.
     */
    public function deleting(PageContract $page): void
    {
        /*
            Doubly-linked list style deletion:
                - Rattach the children to the parent of the page being deleted
                - Promote the pages to a "root" page if the page being deleted has no parent
        */

        $shouldAssociate = $page->parent_id !== null;

        $children = $page->children;

        foreach ($children as $childPage) {
            /**
             * @var Model|PageContract $childPage
             */
            if ($shouldAssociate) {
                $page->parent()->associate($childPage);
            } else {
                $childPage->update([
                    'parent_id' => null,
                ]);
            }
        }
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(PageContract $page): void
    {
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(PageContract $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleting(PageContract $page): void
    {
        //TODO: implement this
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(PageContract $page): void
    {
        //TODO: implement this
    }
}
