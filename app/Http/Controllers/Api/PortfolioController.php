<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Get user's portfolios.
     */
    public function index(Request $request): JsonResponse
    {
        $portfolios = Portfolio::with('skill')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil portofolio',
            'data' => $portfolios
        ]);
    }

    /**
     * Upload a portfolio to verify a skill.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'description' => 'nullable|string|max:500',
        ]);

        $path = $request->file('file')->store('portfolios', 'public');

        $portfolio = Portfolio::create([
            'user_id' => $request->user()->id,
            'skill_id' => $request->skill_id,
            'file_path' => $path,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Portofolio berhasil diunggah. Menunggu verifikasi admin.',
            'data' => $portfolio
        ]);
    }
}
