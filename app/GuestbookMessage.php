<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Support\Facades\Storage;

class GuestbookMessage extends Model
{
    use FormAccessible;

    protected $table = 'guestbook_messages';

    protected $fillable = [
        'text',
    ];

    public function getImageUrl()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getImagePath()
    {
        return $this->image ? Storage::path('public/' . $this->image) : null;
    }

    public function answers()
    {
        return $this->hasMany(static::class, 'answer_id');
    }

    public function parentMessage()
    {
        return $this->belongsTo(static::class, null, null, 'answer');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
