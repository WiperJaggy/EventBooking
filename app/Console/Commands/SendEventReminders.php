<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon; // Import Carbon for date manipulation
use Illuminate\Support\Facades\Log; // Import Log facade

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to users for events happening tomorrow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for events happening tomorrow...');
        Log::info('Scheduler: Checking for events happening tomorrow...');

        // Get tomorrow's date at the start of the day
        $tomorrow = Carbon::tomorrow();
        // Get the end of tomorrow
        $endOfTomorrow = Carbon::tomorrow()->endOfDay();

        // Find events scheduled for tomorrow
        $eventsTomorrow = Event::whereBetween('date', [$tomorrow, $endOfTomorrow])
                               ->with('bookings.user') // Eager load bookings and their associated users
                               ->get();

        if ($eventsTomorrow->isEmpty()) {
            $this->info('No events found for tomorrow.');
            Log::info('Scheduler: No events found for tomorrow.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . $eventsTomorrow->count() . ' event(s) for tomorrow.');
        Log::info('Scheduler: Found ' . $eventsTomorrow->count() . ' event(s) for tomorrow.');

        foreach ($eventsTomorrow as $event) {
            $this->info("Processing event: {$event->title} (ID: {$event->id})");
            Log::info("Scheduler: Processing event: {$event->title} (ID: {$event->id})");

            if ($event->bookings->isEmpty()) {
                $this->warn("No bookings for event: {$event->title}");
                Log::warning("Scheduler: No bookings for event: {$event->title}");
                continue;
            }

            foreach ($event->bookings as $booking) {
                // Ensure the user relationship exists
                if ($booking->user) {
                    $reminderMessage = "Reminder: Your event '{$event->title}' is tomorrow, {$event->date->format('Y-m-d H:i')}.";
                    // Log the reminder (as requested)
                    Log::info("Scheduler: Sending reminder to user {$booking->user->email} for event {$event->title}.");
                    $this->line("Sent reminder to {$booking->user->email} for '{$event->title}'.");

                    // In a real application, you would send an email or push notification here:
                    // Mail::to($booking->user->email)->send(new EventReminderMail($event));
                    // $booking->user->notify(new EventReminderNotification($event));
                } else {
                    Log::warning("Scheduler: Booking ID {$booking->id} has no associated user.");
                }
            }
        }

        $this->info('Event reminders sent successfully!');
        Log::info('Scheduler: Event reminders process completed.');

        return Command::SUCCESS;
    }
}