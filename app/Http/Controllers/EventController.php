<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\EventResource; // IMPORT THIS

class EventController extends Controller
{

public function index(Request $request)
{
    $query = Event::with('creator'); // Keep your eager loading
    
    // Filter by date if date parameter exists
    if ($request->has('date')) {
        $query->whereDate('date', $request->date);
    }
    
    // Determine sorting - default to latest() as in your original
    if ($request->has('sort')) {
        $direction = $request->input('direction', 'asc');
        
        if ($request->sort === 'capacity') {
            $query->orderBy('capacity', $direction);
        } elseif ($request->sort === 'date') {
            $query->orderBy('date', $direction);
        }
    } else {
        $query->latest(); // Maintain your default sorting
    }
    
    // Return the collection using EventResource
    return EventResource::collection($query->get());
}

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $data['created_by'] = Auth::id();
        $event = Event::create($data);

        // Use new EventResource() for a single event, eager load creator for the resource
        return new EventResource($event->load('creator'));
    }

    public function show(Event $event)
    {
        // Use new EventResource() for a single event, eager load creator for the resource
        return new EventResource($event->load('creator'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $dataToUpdate = $request->validated();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('events', 'public');
            $dataToUpdate['image'] = $imagePath;
        }

        $event->update($dataToUpdate);

        // Use new EventResource() for a single event, eager load creator for the resource
        return new EventResource($event->load('creator'));
    }

    public function destroy(Event $event)
    {
        if ($event->created_by !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to delete this event.'], 403);
        }

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return response()->json(null, 204); // Use 204 No Content for deletion without a message
    }
}