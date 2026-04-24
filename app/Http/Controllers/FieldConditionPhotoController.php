<?php

namespace App\Http\Controllers;

use App\Models\WorkAssignment;
use App\Models\FieldConditionPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class FieldConditionPhotoController extends Controller
{
    public function index(WorkAssignment $workAssignment)
    {
        $fieldConditionPhotos = $workAssignment->fieldConditionPhotos()->latest()->get();
        return view('operator-helper.field-condition-photos', compact('workAssignment', 'fieldConditionPhotos'));
    }

    public function store(Request $request, WorkAssignment $workAssignment)
    {
        try {
            $request->validate([
                'photos' => 'required|array',
                'photos.*' => 'image|mimes:jpeg,png,jpg,gif',
            ]);

            $uploadedPhotos = [];
            foreach ($request->file('photos') as $photo) {
                try {
                    $photoPath = $this->saveCompressedImage($photo, 'field-condition-photos');
                    $fieldConditionPhoto = FieldConditionPhoto::create([
                        'work_assignment_id' => $workAssignment->id,
                        'photo_path' => $photoPath,
                        'uploaded_by' => Auth::id(),
                        // Add other fields as necessary, e.g., order, latitude, longitude
                    ]);
                    $uploadedPhotos[] = $fieldConditionPhoto;
                } catch (\Exception $e) {
                    Log::error('Failed to save photo: ' . $e->getMessage());
                    // Continue with the next photo
                }
            }

            if (empty($uploadedPhotos)) {
                throw new \Exception('No photos were successfully uploaded.');
            }

            return redirect()->route('field-condition-photos.index', $workAssignment)
                ->with('success', 'Foto kondisi lapangan berhasil diunggah.');
        } catch (\Exception $e) {
            Log::error('Upload field condition photos failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengunggah foto. Silakan coba lagi.')
                ->withInput();
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
            Log::error('Target path: ' . $path);
            throw $e;
        }
    }

    public function destroy(FieldConditionPhoto $photo)
    {
        try {
            // Delete the file from storage
            $fullPath = public_path($photo->photo_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Delete the database record
            $photo->delete();

            return redirect()->back()->with('success', 'Foto berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Delete field condition photo failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus foto. Silakan coba lagi.');
        }
    }
}
