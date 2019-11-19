<?php

namespace App\Repositories;

use App\GuestbookMessage;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GuestbookMessagesRepository
{
    protected $model;

    public function __construct(GuestbookMessage $model)
    {
        $this->model = $model;
    }

    public function create(User $user, array $data, UploadedFile $image = null, GuestbookMessage $answerMessage = null)
    {
        $message = new GuestbookMessage();
        $message->fill($data);
        $message->user()->associate($user);
        $this->processImage($message, $image);
        if ($answerMessage) {
            $message->parentMessage()->associate($answerMessage);
        }

        if (!$message->save()) {
            throw new \Exception('Не удалось создать запись');
        }
    }

    public function update(User $user, GuestbookMessage $message, array $data, UploadedFile $image = null)
    {
        $message->fill($data);
        $message->user()->associate($user);
        $this->processImage($message, $image);

        if (!$message->update()) {
            throw new \Exception('Не удалось обновить запись');
        }
    }

    /**
     * @param UploadedFile $image
     * @param string $imagePath
     * @param string $imageFullName
     */
    private function storeImage(UploadedFile $image, string $imagePath, string $imageFullName): void
    {
        $image->storeAs(
            'public/' . $imagePath, $imageFullName
        );
    }

    /**
     * @param GuestbookMessage $message
     */
    private function resizeImage(GuestbookMessage $message): void
    {
        // TODO По-хорошему вынести в очереди
        $img = Image::make($message->getImagePath());
        $img->resize(500, 500, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save();
    }

    /**
     * @param UploadedFile $image
     * @param string $imageFullPath
     * @param GuestbookMessage $message
     */
    private function associateImage(UploadedFile $image, string $imageFullPath, GuestbookMessage $message): void
    {
        $message->image = $image ? $imageFullPath : null;
    }

    private function resetImage(GuestbookMessage $message)
    {
        $message->image = null;
    }

    /**
     * @param UploadedFile $image
     * @param GuestbookMessage $message
     */
    private function processImage(GuestbookMessage $message, UploadedFile $image = null): void
    {
        if (!$image) {
            $this->resetImage($message);

            return;
        }

        $imagePath = 'guestbook/images';
        $imageName = md5(Str::random(10) . microtime());
        $imageFullName = $imageName . '.' . $image->extension();
        $imageFullPath = $imagePath . '/' . $imageFullName;

        $this->associateImage($image, $imageFullPath, $message);
        $this->storeImage($image, $imagePath, $imageFullName);
        $this->resizeImage($message);
    }
}
