<?php

namespace App\Console\Commands;
use App\Models\Event;
use Illuminate\Console\Command;

class DeleteOldEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $deleted = Event::where('date', '<', now()->subDays(30))->delete();
        $this->info("Deleted {$deleted} old events");
    }
}
