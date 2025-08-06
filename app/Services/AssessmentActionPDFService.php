<?php

namespace App\Services;

use App\Models\Assessment;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AssessmentActionPDFService
{
    private Assessment $assessment;
    private array $actions = [];

    /**
     * Set the assessment and collect actions based on responses
     */
    public function setAssessment(Assessment $assessment): void
    {
        $this->assessment = $assessment;

        $responses = $assessment->responses()->with('criterion.actions')->get();
        foreach ($responses as $response) {
            $criterion = $response->criterion;
            if (!$criterion) {
                continue;
            }

            $actionModel = null;
            if ($response->response === 'yes') {
                $actionModel = $criterion->actions->firstWhere('flag', true);
            } elseif ($response->response === 'no') {
                $actionModel = $criterion->actions->firstWhere('flag', false);
            }

            if ($actionModel) {
                $this->actions[] = [
                    'criterion_en' => $criterion->name_en,
                    'criterion_ar' => $criterion->name_ar,
                    'action_en' => $actionModel->action_en,
                    'action_ar' => $actionModel->action_ar,
                    'type_en' => $actionModel->getActionTypeText('en'),
                    'type_ar' => $actionModel->getActionTypeText('ar'),
                ];
            }
        }
    }

    /**
     * Generate the PDF and return as download response
     */
    public function generatePDF(): Response
    {
        $html = $this->buildHTML();
        $pdf = $this->createPdfFromHtml($html);
        $filename = $this->generateFilename();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ]);
    }

    /**
     * Optionally save PDF to storage and return path
     */
    public function savePDF(string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename();
        $html = $this->buildHTML();
        $pdf = $this->createPdfFromHtml($html);
        Storage::disk('public')->put("pdfs/{$filename}", $pdf);
        return "pdfs/{$filename}";
    }

    private function buildHTML(): string
    {
        $titleEn = 'Corrective & Improvement Actions';
        $titleAr = 'الإجراءات التصحيحية والتحسينية';

        $rows = '';
        foreach ($this->actions as $action) {
            $rows .= "<tr>
                <td>" . htmlspecialchars($action['criterion_en']) . "<br><span dir='rtl'>" . htmlspecialchars($action['criterion_ar']) . "</span></td>
                <td>" . htmlspecialchars($action['action_en']) . "<br><span dir='rtl'>" . htmlspecialchars($action['action_ar']) . "</span></td>
                <td>" . htmlspecialchars($action['type_en']) . "<br><span dir='rtl'>" . htmlspecialchars($action['type_ar']) . "</span></td>
            </tr>";
        }

        $css = "@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@400;600&display=swap');
        body{font-family:'Poppins','Amiri',sans-serif;font-size:14px;color:#333;margin:40px;}
        h2{text-align:center;margin-bottom:20px;}
        table{width:100%;border-collapse:collapse;}
        th,td{border:1px solid #ddd;padding:8px;}
        th{background:#f5f5f5;text-align:left;}
        td span{font-family:'Amiri',serif;font-size:13px;color:#1a1a1a;}
        "
        ;

        return "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><style>{$css}</style></head><body>
            <h2>{$titleEn} / {$titleAr}</h2>
            <table>
                <thead><tr><th>Criterion / المعيار</th><th>Action / الإجراء</th><th>Type / النوع</th></tr></thead>
                <tbody>{$rows}</tbody>
            </table>
        </body></html>";
    }

    private function createPdfFromHtml(string $html): string
    {
        return Browsershot::html($html)
            ->format('A4')
            ->margins(20, 20, 20, 20)
            ->printBackground()
            ->waitUntilNetworkIdle()
            ->timeout(120)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
            ->pdf();
    }

    private function generateFilename(): string
    {
        $toolName = $this->assessment->tool ? $this->assessment->tool->name_en : 'assessment';
        $toolName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $toolName);
        return "actions_{$toolName}_{$this->assessment->id}.pdf";
    }
}
