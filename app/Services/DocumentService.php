<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function upload($image)
    {
        $user = Auth::guard('sanctum')->user();

        $time = time();
        $randomNumber = $this->random4Digits();
        $fileName = $time . $randomNumber . "." . $image->getClientOriginalExtension();

        $path = Storage::disk('public')->put("image", $image);
        $fileName = str_replace('image/', '', $path);
        return Document::create([
            'file_name' => $fileName,
            'user_id' => $user->id ?? null,
            'url' => $path,
            'mime_type' =>  $image->getClientMimeType(),
            'size' => $image->getSize(),
            'original_file_name' => $image->getClientOriginalName(),
        ]);
    }

    public static function random4Digits()
    {
        $digits = 4;
        $randomValue = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        return $randomValue;
    }


    public function deleteResource($documentIds)
    {
        $documents = Document::whereIn('id', $documentIds)->get();
        foreach ($documents as $item) {
            $image_path = public_path('/image/' . $item->file_name);
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
        }
        Document::whereIn('id', $documentIds)->delete();
    }
}
