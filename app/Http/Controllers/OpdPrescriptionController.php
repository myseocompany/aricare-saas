<?php

namespace App\Http\Controllers;

use App\Models\OpdPrescription;
use Barryvdh\DomPDF\Facade\Pdf;


class OpdPrescriptionController extends AppBaseController
{

    public function convertToPDF($id)
    {
        if (app()->getLocale() == "zh") {
            app()->setLocale("en");
        }
        $opdPrescription = OpdPrescription::find($id);

        $pdf = Pdf::loadView('opd_prescriptions.opd_prescription_pdf', compact('opdPrescription'));

        return $pdf->stream(__('messages.delete.prescription'));
    }
}
