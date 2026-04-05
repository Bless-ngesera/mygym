<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schedule as ScheduleFacade;

// Send class reminders every 30 minutes
ScheduleFacade::command('reminders:send')->everyThirtyMinutes()->description('Send class reminders to members');

// Send class reminders for the next day at 8 PM
ScheduleFacade::command('reminders:tomorrow')->dailyAt('20:00')->description('Send next day class reminders');

// Clean up old notifications daily at midnight
ScheduleFacade::command('notifications:clean')->daily()->at('00:00')->description('Clean old notifications');

// Generate weekly reports every Monday at 8 AM
ScheduleFacade::command('reports:weekly')->weekly()->mondays()->at('08:00')->description('Generate weekly reports');

// Backup database daily at 1 AM
ScheduleFacade::command('backup:run')->daily()->at('01:00')->description('Backup database');

// Clear old logs weekly on Sunday at 2 AM
ScheduleFacade::command('log:clear')->weekly()->sundays()->at('02:00')->description('Clear old logs');

// Send monthly statements on the 1st of each month at 9 AM
ScheduleFacade::command('statements:send')->monthlyOn(1, '09:00')->description('Send monthly statements');

// Check for expired memberships daily at midnight
ScheduleFacade::command('memberships:check')->daily()->at('00:30')->description('Check expired memberships');

// Generate revenue report daily at 11 PM
ScheduleFacade::command('reports:revenue')->daily()->at('23:00')->description('Generate daily revenue report');
