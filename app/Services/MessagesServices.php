<?php

namespace App\Services;

use App\Jobs\ProcessMessagesMediaJob;
use App\Models\Conversation;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class MessagesServices{
    //
    public function createNewMessage(Conversation $conversation ,array $requestValidate ,User $authUser ,?UploadedFile $mediaFile): Messages
    {
        //
        $path = null ;
        $type = 'text';
        if($mediaFile){
            $path = $mediaFile->store('images/messagesMedia/'.$type.'/original' ,'public');
            $type = $this->getMediaType($mediaFile);
        }
        $message = $conversation->messages()->create([
            'user_id' => $authUser->id,
            'body' => $requestValidate['body'] ?? null,
            'type' => $type,
            'media_path' => $path,
        ]);
        $message->load('user');
        ProcessMessagesMediaJob::dispatch();

        return $message;
    }

    public function maskMsgAsRead(Messages $message ,User $authUser): void
    {
        //
        if($message->read_at === null && $message->user_id !== $authUser->id){
            $message->update([
                'read_at' => now(),
            ]);
        }
    }

    public function storeMedia()
    {
        //
    }

    public function deleteMediaIfExists(Messages $message): bool
    {
        //
        if($message->media_path){
            Storage::disk('public')->delete($message->media_path);
            return true;
        }return false;
    }

    private function getMediaType(UploadedFile $file): string
    {
        //
        $mimeType = $file->getMimeType();

        return match (true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'video/') => 'video',
            in_array($mimeType, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
            ]) => 'document',
            default => throw new InvalidArgumentException(
                "Unsupported media type: {$mimeType}"
            ),
        };
    }
}