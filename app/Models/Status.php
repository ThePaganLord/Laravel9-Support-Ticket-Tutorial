<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'category_id', 'ticket_id', 'title', 'priority', 'message', 'status',
    ];
    
    // Set the relationships to User and Personal
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
       
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id', 'id');
    }
    
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    public function status(): HasMany
    {
        return $this->hasMany(Status::class);
    }
    
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

}
