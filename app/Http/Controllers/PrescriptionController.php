<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PrescriptionRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class PrescriptionController extends Controller
{
    private $prescriptionRepository;

    public function __construct(
        PrescriptionRepository $prescriptionRepo,
    ) {
        $this->prescriptionRepository = $prescriptionRepo;
    }
    public function convertToPDF($id): \Illuminate\Http\Response
    {
        if (app()->getLocale() == "zh") {
            app()->setLocale("en");
        }
        $data = $this->prescriptionRepository->getSettingList();

        $prescription = $this->prescriptionRepository->getData($id);

        $medicines = $this->prescriptionRepository->getMedicineData($id);

        $imageData = Http::get($data['app_logo'])->body();
        $imageType = pathinfo($data['app_logo'], PATHINFO_EXTENSION);
        $base64Image = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);

        $data['app_logo'] = $base64Image;

        $pdf = Pdf::loadView('prescriptions.prescription_pdf', compact('prescription', 'medicines', 'data'));

        return $pdf->stream($prescription['prescription']->patient->user->full_name . '-' . $prescription['prescription']->id);
    }
}
