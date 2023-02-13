<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use App\Services\PhoneNumbers\Contracts\PhoneNumbers as PhoneNumbersInterface;
use Illuminate\Support\Facades\DB;

class PhoneNumbersController extends BaseController
{
    /**
     * Returns phone numbers.
     *
     * @return View
     */
    public function index(): View
    {
        $phoneNumbers = DB::table('phone_numbers')->simplePaginate(15);

        return view('welcome', ['phoneNumbers' => $phoneNumbers]);
    }

    /**
     * Import and store the file.
     *
     * @param PhoneNumbersInterface $phoneNumberService
     * @return RedirectResponse
     */
    public function importFile(PhoneNumbersInterface $phoneNumberService): RedirectResponse
    {
        $fileName = $phoneNumberService->storeNumbers();
        $phoneNumbers = $phoneNumberService->getNumbers($fileName);
        $formattedNumbers = $phoneNumberService->formatNumbers($phoneNumbers);

        PhoneNumber::insert($formattedNumbers);

        return redirect()->back()->with('success','Data Imported Successfully');
    }
}
