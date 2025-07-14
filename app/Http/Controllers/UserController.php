<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class UserController extends Controller
{

public function uploadProfilePhoto(Request $request)
{
    $request->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('profile_picture')) {
        $file = $request->file('profile_picture');
        $path = $file->store('profile_pictures', 'public'); // Store in storage/app/public/profile_pictures
        
        // Update user's profile picture path in database
        auth()->user()->update([
            'profile_picture' => $path
        ]);

        return response()->json(['message' => 'Profile picture updated successfully']);
    }

    return response()->json(['message' => 'No file uploaded'], 400);
}
}
