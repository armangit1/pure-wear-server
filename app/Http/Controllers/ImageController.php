<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{

    public function destroy(string $id)
    {

        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'status' => 404,
                'message' => 'Image not found'
            ], 404);
        }

        if ($image->user_id !== Auth::id()) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized action'
            ], 403);
        }


        $imagePath = storage_path('app/public/' . $image->image);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($image) {
            $image->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Image deleted successfully'
            ], 200);
        } 
    }
}
