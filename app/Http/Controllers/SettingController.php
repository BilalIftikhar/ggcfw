<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function indexInstitute(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_institute_settings')) {
            abort(403, 'You are not authorized to view institute settings.');
        }

        $setting = Setting::firstOrCreate([]);
        return view('settings.institute', compact('setting'));
    }

    public function indexEmail(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_email_settings')) {
            abort(403, 'You are not authorized to view email settings.');
        }

        $setting = Setting::firstOrCreate([]);
        return view('settings.email', compact('setting'));
    }

    public function indexWhatsapp(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_whatsapp_settings')) {
            abort(403, 'You are not authorized to view WhatsApp settings.');
        }

        $setting = Setting::firstOrCreate([]);
        return view('settings.whatsapp', compact('setting'));
    }

    public function updateInstitute(Request $request)
    {
        if (!$request->user()->can('update_institute_settings')) {
            abort(403, 'You are not authorized to update institute settings.');
        }

        try {
            $validated = $request->validate([
                'institute_name'        => 'required|string|max:255',
                'tagline'               => 'nullable|string|max:255',
                'institute_email'       => 'nullable|email|max:255',
                'institute_phone'       => 'nullable|string|max:20',
                'institute_website'     => 'nullable|string|max:255',
                'institute_address'     => 'nullable|string|max:500',
                'voucher_footer_note'   => 'nullable|string|max:500',
                'default_currency'      => 'nullable|string|max:10',
                'timezone'              => 'nullable|string|max:100',
                'academic_year_start'   => 'nullable|date',
                'academic_year_end'     => 'nullable|date|after_or_equal:academic_year_start',
                'institute_logo'        => 'nullable|image|max:2048',
                'voucher_logo'          => 'nullable|image|max:2048',
            ]);

            DB::beginTransaction();

            $setting = Setting::firstOrFail(); // or use your method to get settings

            $setting->fill($validated);
            $setting->maintenance_mode = $request->has('maintenance_mode');

            if ($request->hasFile('institute_logo')) {
                $setting->clearMediaCollection('institute_logo');
                $setting->addMediaFromRequest('institute_logo')->toMediaCollection('institute_logo');
            }

            if ($request->hasFile('voucher_logo')) {
                $setting->clearMediaCollection('voucher_logo');
                $setting->addMediaFromRequest('voucher_logo')->toMediaCollection('voucher_logo');
            }

            $setting->save();

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Institute settings updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => $e->validator->errors()->first(),
                    'title' => 'Validation Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to update institute settings. ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 4000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }
    }

    public function updateEmail(Request $request)
    {
        if (!$request->user()->can('update_email_settings')) {
            abort(403, 'You are not authorized to update email settings.');
        }

        try {
            $validated = $request->validate([
                'smtp_host'          => 'required|string|max:255',
                'smtp_port'          => 'required|numeric',
                'smtp_username'      => 'nullable|string|max:255',
                'smtp_password'      => 'nullable|string|max:255',
                'smtp_encryption'    => 'nullable|in:tls,ssl',
                'smtp_from_address'  => 'required|email|max:255',
                'smtp_from_name'     => 'required|string|max:255',
                'smtp_active'        => 'nullable|boolean',
            ]);

            DB::beginTransaction();

            $setting = Setting::firstOrFail(); // adjust as per your logic

            $setting->smtp_host          = $validated['smtp_host'];
            $setting->smtp_port          = $validated['smtp_port'];
            $setting->smtp_username      = $validated['smtp_username'];
            $setting->smtp_password      = $validated['smtp_password'];
            $setting->smtp_encryption    = $validated['smtp_encryption'];
            $setting->smtp_from_address  = $validated['smtp_from_address'];
            $setting->smtp_from_name     = $validated['smtp_from_name'];
            $setting->smtp_active        = $request->has('smtp_active') ? 1 : 0;

            $setting->save();

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Email settings updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back();

        } catch (ValidationException $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => $e->validator->errors()->first(),
                    'title' => 'Validation Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to update email settings. ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 4000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }
    }

    public function updateWhatsapp(Request $request)
    {
        if (!$request->user()->can('update_whatsapp_settings')) {
            abort(403, 'You are not authorized to update WhatsApp settings.');
        }

        try {
            $validated = $request->validate([
                'whatsapp_api_key' => 'required|string|max:255',
                'whatsapp_number'  => 'required|string|max:20',
                'whatsapp_url'     => 'required|url|max:500',
                'whatsapp_active'  => 'nullable|boolean',
            ]);

            DB::beginTransaction();

            $setting = Setting::firstOrFail(); // Or use your custom method to retrieve settings

            $setting->whatsapp_api_key = $validated['whatsapp_api_key'];
            $setting->whatsapp_number  = $validated['whatsapp_number'];
            $setting->whatsapp_url     = $validated['whatsapp_url'];
            $setting->whatsapp_active  = $request->has('whatsapp_active') ? 1 : 0;

            $setting->save();

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'WhatsApp settings updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back();

        } catch (ValidationException $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => $e->validator->errors()->first(),
                    'title' => 'Validation Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to update WhatsApp settings. ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 4000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }
    }





}
