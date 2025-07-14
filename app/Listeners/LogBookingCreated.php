<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log; // Import Log facade

class LogBookingCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        Log::info('Booking Created Event: ', [
            'booking_id' => $event->booking->id,
            'user_id' => $event->booking->user_id,
            'event_id' => $event->booking->event_id,
            'event_title' => $event->booking->event->title ?? 'N/A', // Eager load event if needed
            'user_email' => $event->booking->user->email ?? 'N/A', // Eager load user if needed
            'booked_at' => $event->booking->created_at->toDateTimeString(),
        ]);

        // You could send a notification here too:
        // Notification::send($event->booking->user, new BookingConfirmationNotification($event->booking));
    }
}