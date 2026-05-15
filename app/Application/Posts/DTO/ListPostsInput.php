<?php

namespace App\Application\Posts\DTO;

use Illuminate\Http\Request;

final readonly class ListPostsInput
{
    public function __construct(
        public ?string $search,
        public ?int $skillId,
        public string $sortBy,
        public bool $bookmarkedOnly,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $search = $request->query('search');
        $skillId = $request->query('skill_id');
        $sortBy = $request->query('sort', 'latest');

        return new self(
            search: is_string($search) && trim($search) !== '' ? $search : null,
            skillId: is_numeric($skillId) ? (int) $skillId : null,
            sortBy: $sortBy === 'oldest' ? 'oldest' : 'latest',
            bookmarkedOnly: $request->boolean('bookmarked'),
        );
    }
}
