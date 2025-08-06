<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\AssessmentActionPDFService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssessmentActionPDFController extends Controller
{
    /**
     * Download corrective and improvement actions PDF
     */
    public function download(Request $request, Assessment $assessment): Response|JsonResponse
    {
        try {
            $this->authorizeAssessment($assessment);

            if (!$assessment->isPremiumAssessment()) {
                return response()->json([
                    'error' => 'Premium access required',
                    'message' => 'Action PDF is available for premium assessments only.'
                ], 403);
            }

            if ($assessment->status !== 'completed') {
                return response()->json([
                    'error' => 'Assessment not completed',
                    'message' => 'PDF can only be generated for completed assessments.'
                ], 400);
            }

            $service = new AssessmentActionPDFService();
            $service->setAssessment($assessment);
            return $service->generatePDF();
        } catch (\Exception $e) {
            Log::error('Failed to generate action PDF', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => 'Please try again later.'
            ], 500);
        }
    }

    /**
     * Check if user can access the assessment
     */
    private function authorizeAssessment(Assessment $assessment): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Authentication required');
        }

        if ($assessment->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access to this assessment');
        }
    }
}
