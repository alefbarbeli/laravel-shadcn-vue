<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LyricsController extends Controller
{
    public function generateLyrics(Request $request)
    {
        $text = $request->input('text');

        if (strlen($text) > 300 || empty(trim($text))) {
            return response()->json(['success' => false, 'message' => 'Texto inválido.']);
        }

        $response = Http::post('https://jafm.weareamaze.app/api/lyrics', ['text' => $text]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return $this->handleErrorResponse($response);
    }

    public function generateAudio(Request $request)
    {
        $selectedSentence = $request->input('selectedSentence');

        $response = Http::post('https://jafm.weareamaze.app/api/generate', ['selectedSentence' => $selectedSentence]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return $this->handleErrorResponse($response);
    }

    public function createJingle(Request $request)
    {
        $text = $request->input('text');

        if (strlen($text) > 300 || empty(trim($text))) {
            return response()->json(['success' => false, 'message' => 'Texto inválido.']);
        }

        $response = Http::post('https://jafm.weareamaze.app/api/create', ['text' => $text]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return $this->handleErrorResponse($response);
    }

    private function handleErrorResponse($response)
    {
        return response()->json($response->json());
        
        switch ($response->status()) {
            case 400:
                return response()->json(['success' => false, 'message' => 'Bad request. Please check your input.']);
            case 408:
                return response()->json(['success' => false, 'message' => 'Request timeout. Please try again later.']);
            case 500:
                return response()->json(['success' => false, 'message' => 'Internal server error. Please try again later.']);
            default:
                return response()->json(['success' => false, 'message' => 'An unexpected error occurred.']);
        }
    }
}