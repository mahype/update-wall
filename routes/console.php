<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('machines:mark-stale')->everyFifteenMinutes();
Schedule::command('reports:prune --days=90')->daily();
