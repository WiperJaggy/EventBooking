<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Events\BookingCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Resources\BookingResource; // IMPORT THIS

class BookingController extends Controller
{
    public function index()
    {
        // Use BookingResource::collection() for a list of bookings, eager load event and user
        return BookingResource::collection(Auth::user()->bookings()->with('event', 'user')->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => [
                'required',
                'exists:events,id',
                Rule::unique('bookings')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ], [
            'event_id.unique' => 'You have already booked this event.'
        ]);

        $event = Event::findOrFail($request->event_id);

        if ($event->date->isPast()) {
            return response()->json(['message' => 'Cannot book an event that has already occurred.'], 400);
        }

        if ($event->spots_available <= 0) {
            return response()->json(['message' => 'No spots available for this event.'], 409);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
        ]);

        BookingCreated::dispatch($booking);

        // Use new BookingResource() for a single booking, eager load event and user
        return new BookingResource($booking->load('event', 'user'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to view this booking.'], 403);
        }
        // Use new BookingResource() for a single booking, eager load event and user
        return new BookingResource($booking->load('event', 'user'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to cancel this booking.'], 403);
        }

        if ($booking->event->date->isPast()) {
             return response()->json(['message' => 'Cannot cancel a booking for an event that has already occurred.'], 400);
        }

        $booking->delete();

        // Return 204 No Content for deletion without a message, adhering to REST best practices for deletes.
        return response()->json(null, 204);
        // If you absolutely need a message, change to return response()->json(['message' => 'Booking cancelled successfully.'], 200);
    }
}