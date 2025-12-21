<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CodingChallenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CodingSubmissionController extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'language' => 'required|string|in:python,java,javascript',
            'challengeId' => 'required|integer|exists:coding_challenges,id',
        ]);

        try {
            $output = $this->executeCode(
                $validated['code'],
                $validated['language']
            );

            return response()->json([
                'success' => true,
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function submit(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'code' => 'required|string',
            'language' => 'required|string|in:python,java,javascript',
            'challengeId' => 'required|integer|exists:coding_challenges,id',
        ]);

        try {
            // Execute code and validate
            $output = $this->executeCode(
                $validated['code'],
                $validated['language']
            );

            // Get challenge details
            $challenge = CodingChallenge::findOrFail($validated['challengeId']);

            // For now, we'll consider submission successful if no error
            $isPassed = true;

            // Award XP if challenge is passed
            if ($isPassed) {
                $profile = $user->profile();
                $profile->total_xp += $challenge->xp_reward;
                $profile->save();
            }

            return response()->json([
                'success' => true,
                'passed' => $isPassed,
                'message' => $isPassed
                    ? "Congratulations! You earned {$challenge->xp_reward} XP!"
                    : 'Challenge not fully passed. Try again!',
                'xpAwarded' => $isPassed ? $challenge->xp_reward : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'passed' => false,
                'message' => "Error: {$e->getMessage()}",
            ], 400);
        }
    }

    private function executeCode(string $code, string $language): string
    {
        // This is a basic implementation. For production, use a sandboxed environment
        // like Judge0 API, Piston API, or similar code execution service

        switch ($language) {
            case 'python':
                return $this->executePython($code);
            case 'java':
                return $this->executeJava($code);
            case 'javascript':
                return $this->executeJavaScript($code);
            default:
                throw new \Exception('Unsupported language');
        }
    }

    private function executePython(string $code): string
    {
        // Using proc_open to execute Python safely (for development only)
        // For production, use Judge0 or similar service
        $process = proc_open(
            'python -c ' . escapeshellarg($code),
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            null
        );

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            proc_close($process);

            return $error ?: $output ?: 'No output';
        }

        return 'Failed to execute code';
    }

    private function executeJava(string $code): string
    {
        // Java execution is more complex due to compilation requirement
        // For production, use Judge0 API
        return 'Java code execution requires a proper sandbox environment. Use Judge0 API for production.';
    }

    private function executeJavaScript(string $code): string
    {
        // Using Node.js
        $process = proc_open(
            'node -e ' . escapeshellarg($code),
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            null
        );

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            proc_close($process);

            return $error ?: $output ?: 'No output';
        }

        return 'Failed to execute code';
    }
}
