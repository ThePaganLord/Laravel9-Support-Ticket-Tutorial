<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['ticket_id', 'user_id', 'comment'];
    
    // Relationship to ticket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
