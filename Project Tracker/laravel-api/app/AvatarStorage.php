<?php

namespace App;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Saves / deletes profile pictures.
 * Pictures live in public/uploads/avatars so no "storage:link" step is needed.
 */
class AvatarStorage
{
    public const DIR = 'uploads/avatars';

    /**
     * Save the uploaded picture and return the path to store on the user.
     * The old picture (if any) is deleted.
     */
    public static function store(UploadedFile $file, ?string $old = null): string
    {
        $dir = public_path(self::DIR);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.strtolower($file->extension());

        $file->move($dir, $name);

        self::delete($old);

        return self::DIR.'/'.$name;
    }

    /**
     * Delete a picture we saved earlier (never touches anything outside uploads/avatars).
     */
    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, self::DIR.'/')) {
            $full = public_path($path);

            if (is_file($full)) {
                @unlink($full);
            }
        }
    }
}
