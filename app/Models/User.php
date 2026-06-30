<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function groupInfo(){
        return $this->groups()->first();
    }

    public function groupId(){
        return $this->groups()->first()->id;
    }

    public function isBuyer(): bool
    {
        return $this->groups()->where('type', 'buyer')->exists();
    }


    public function isSeller(): bool
    {
        $hasSellerGroup = $this->groups()->where('type', 'seller')->exists();
        $hasSellers = $this->sellers()->exists();
        $isAdmin = $this->isAdmin();

        return ($hasSellerGroup && $hasSellers) || ($hasSellers && $isAdmin);
    }

    public function isAdmin(): bool
    {
        return $this->groups()->where('type', 'admin')->exists();
    }

    public function getVendorId(): ?int
    {
        if ($this->isSeller()) {
            return $this->vendor_id; // предполагаем, что vendor_id есть в таблице users
        }
        return null;
    }

    public function sellers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Seller::class, 'seller_user')
            ->withTimestamps();
    }
    public function getFirstSeller(): ?Seller
    {
        return $this->sellers()->first();
    }


    public function getSellerId(): ?int
    {
        $seller = $this->getFirstSeller();
        return $seller ? $seller->id : null;
    }

    public function chats() {
        return $this->belongsToMany(Chat::class, 'chat_user');
    }


}
