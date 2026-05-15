<?php

namespace App\Http\Controllers\Api;

use App\Application\Skills\UseCases\ListSkills;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    public function index(ListSkills $listSkills): JsonResponse
    {
        return response()->json([
            'message' => 'Berhasil mengambil daftar skill',
            'data' => $listSkills(),
        ]);
    }
}
