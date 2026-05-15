<?php

namespace App\Application\Posts\UseCases;

use App\Models\Post;
use App\Models\User;

final readonly class GenerateWhatsAppLink
{
    public function __invoke(Post $post, User $bidder): string
    {
        $post->loadMissing(['user', 'neededSkill', 'offeredSkill']);

        $owner = $post->user;
        $text = "Halo {$owner->name}, saya {$bidder->name} dari SwapSkill. Saya lihat kamu butuh bantuan *{$post->neededSkill->name}* dan menawarkan *{$post->offeredSkill->name}*. Saya tertarik untuk barter!";
        $phone = preg_replace('/^0/', '62', $owner->whatsapp_number);

        return "https://wa.me/{$phone}?text=" . urlencode($text);
    }
}
