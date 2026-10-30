<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class Uploads
{
    /** Move an upload into public/uploads/{dir} and return its public-relative path. */
    public static function store(UploadedFile $file, string $dir): string
    {
        $ext = $file->guessExtension() ?: $file->extension();
        $name = uniqid($dir . '_') . '.' . $ext;
        $file->move(public_path("uploads/{$dir}"), $name);

        return "uploads/{$dir}/{$name}";
    }

    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}