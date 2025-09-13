<?php

use App\Jobs\UpdateStaticsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new UpdateStaticsJob(),'default')->everyFiveMinutes();
