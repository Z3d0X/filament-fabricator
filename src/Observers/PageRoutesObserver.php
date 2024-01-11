<?php

namespace Z3d0X\FilamentFabricator\Observers;

use Z3d0X\FilamentFabricator\Models\Contracts\Page;
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
    public function created(Page $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        //TODO: implement this
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        $this->pageRoutesService->updateUrlsOf($page);
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        //TODO: implement this
    }
}
