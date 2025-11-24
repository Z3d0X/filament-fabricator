<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Z3d0X\FilamentFabricator\Tests\TestCase;

beforeAll(function () {
    if (empty(config('app.key'))) {
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }
});

uses(TestCase::class)->in(__DIR__)->beforeEach(function () {
    $this->withSession([]);
});
