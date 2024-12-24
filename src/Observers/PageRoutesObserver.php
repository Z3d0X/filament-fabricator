<?php

namespace Z3d0X\FilamentFabricator\Observers;

use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Services\PageRoutesService;

class PageRoutesObserver
{
    public function __construct(
        protected PageRoutesService $pageRoutesService
    ) {}

    /**
     * Handle the Page "created" event.
     */
    public function created(PageContract&Model $page): void
    {
        // Creating the page simply requires setting the URLs in all caches.
        // This will be done properly through the update procedure.
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(PageContract&Model $page): void
    {
        // If the parent_id has changed, and if the relationship has already been loaded
        // then after an update we might not read the right parent. That's why we always
        // load it on update, this ensures we clear the old URLs properly (they were cached)
        // and set the new ones properly (we have the right parent to do so).
        if ($page->wasChanged('parent_id')) {
            $page->load('parent');
        }

        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "deleting" event.
     */
    public function deleting(PageContract&Model $page): void
    {
        // We do the logic in `deleting` instead of `deleted` since we need access to the object
        // both in memory and in database (e.g. to load relationship data).

        // Before properly deleting it, remove its URLs from
        // all the mappings and caches.
        $this->pageRoutesService->removeUrlsOf($page);

        // Doubly-linked list style deletion:
        //      - Re-attache the given page children to the parent of the given page
        //      - Promote the pages to a "root page" (i.e. page with no parent) if the given page had no parent

        // Only load one level of children since they're the ones that will be re-attached
        $page->load('children');
        $children = $page->children;

        foreach ($children as $childPage) {
            /**
             * @var Model|PageContract $childPage
             */

            // We use `?? null` followed by `?: null` to go around the cast to integer
            // and make sure we have `null` instead of `0` when there's no parent.
            $parentId = $page->parent_id ?? null;
            $parentId = $parentId ?: null;

            // Using update, instead of associate or dissociate, we trigger DB events (which we need)
            $childPage->update([
                'parent_id' => $parentId,
            ]);
        }
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(PageContract&Model $page): void
    {
        // Since `deleting` is called before `deleted`, and
        // since everything is handled there, do nothing.
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(PageContract&Model $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleting(PageContract&Model $page): void
    {
        // You always go through `deleting` before going through `forceDeleting`.
        // Since everything is properly handled in `deleting`, do nothing here.
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(PageContract&Model $page): void
    {
        // You always go through `deleted` before going through `forceDeleted`.
        // Since everything is properly handled in `deleted`, do nothing here.
    }
}
