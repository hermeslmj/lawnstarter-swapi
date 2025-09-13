<?php

use App\Jobs\UpdateStatics;
use Illuminate\Bus\Dispatcher;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new UpdateStatics(),'default')->everyFiveMinutes();
