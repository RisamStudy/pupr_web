<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function user_edit(Request $request): View
    {
        return view('operator-helper.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function user_update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $request->user()->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if it exists
                if ($request->user()->profile_photo_path) {
                    $oldPath = public_path($request->user()->profile_photo_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Store new compressed photo
                $path = $this->saveCompressedImage(
                    $request->file('profile_photo'),
                    'profile-photos'
                );
                $request->user()->profile_photo_path = $path;
            }

            $request->user()->save();

            return Redirect::route('operator-helper.profile.edit')
                ->with('status', 'profile-updated');

        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return Redirect::route('operator-helper.profile.edit')
                ->with('error', 'Gagal mengupdate profil. Silakan coba lagi.');
        }
    }

    private function saveCompressedImage($file, $directory)
    {
        try {
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $path = config('filesystems.uploads_base_path') . '/' . $directory;

            if (!file_exists($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new \Exception("Failed to create directory: $path");
                }
            }

            // Create an instance of ImageManager
            $manager = new ImageManager(new GdDriver());
            // Read the image
            $img = $manager->read($file);

            // Calculate new dimensions while maintaining aspect ratio
            $width = $img->width();
            $height = $img->height();
            $targetWidth = 800; // You can adjust this value
            $targetHeight = intval($height * ($targetWidth / $width));

            // Resize the image
            $img->resize($targetWidth, $targetHeight);

            // Convert and save as WebP
            $img->toWebp(80)->save($path . '/' . $filename);

            // Verify the file was actually saved
            if (!file_exists("$path/$filename")) {
                throw new \Exception("File was not found after saving: $path/$filename");
            }

            Log::info("File successfully saved and compressed: $path/$filename");

            // Return the path relative to the public directory
            return "/uploads/$directory/$filename";
        } catch (\Exception $e) {
            Log::error('Error saving compressed image: ' . $e->getMessage());
            Log::error('File details: ' . json_encode($file->getClientOriginalName()));
            Log::error('Target path: ' . $path ?? 'unknown');
            throw $e;
        }
    }

    /**
     * Delete the user's account.
     */
    public function user_destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
