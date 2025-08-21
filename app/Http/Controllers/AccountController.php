<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View; 
use App\Models\Payroll;
use Imagick;
use App\Models\UserFcmToken;

class AccountController extends Controller
{ 
    public function index(Request $request): View
    {
        return view('accounts.account', [
            'user' => $request->user(),
        ]);
    }
  
    public function setting(): View
    {
        return view('accounts.setting');
    }

    public function edit(Request $request): View
    {
        return view('accounts.edit', [
            'user' => $request->user(),
        ]);
    }

    public function changePassword(Request $request): View
    {
        return view('accounts.password');
    }
 
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Validasi input termasuk gender dan phone
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'gender' => 'nullable|in:male,female',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update data dasar
        $user->name   = $request->name;
        $user->gender = $request->gender;
        $user->phone  = $request->phone;

        // Update email jika berubah
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null; // reset verifikasi email
        }

        // Proses avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file      = $request->file('avatar');
            $filename  = time() . '.jpg';
            $avatarDir = public_path('upload/avatar');
            $thumbDir  = public_path('upload/avatar/thumbnails');

            if (!file_exists($avatarDir)) mkdir($avatarDir, 0777, true);
            if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);

            // Hapus avatar lama jika bukan default
            if ($user->profile_photo && $user->profile_photo !== 'default.png') {
                @unlink($avatarDir . '/' . $user->profile_photo);
                @unlink($thumbDir . '/' . $user->profile_photo);
            }

            try {
                // Simpan gambar utama
                $image = new \Imagick($file->getRealPath());
                $image->setImageFormat('jpeg');
                $image->setImageCompressionQuality(90);
                $image->writeImage($avatarDir . '/' . $filename);

                // Buat thumbnail 400x400 proporsional
                $thumb = new \Imagick($file->getRealPath());
                $thumb->thumbnailImage(400, 400, true);
                $thumb->setImageFormat('jpeg');
                $thumb->setImageCompressionQuality(70);
                $thumb->writeImage($thumbDir . '/' . $filename);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memproses gambar: ' . $e->getMessage());
            }

            $user->profile_photo = $filename;
        }

        $user->save();

        return back()->with('status', 'Profile diperbarui.');
    }

  
    public function payrollIndex(Request $request)
    {
        $year = $request->input('year') ?? date('Y');

        $payrolls = Payroll::where('year', $year)
                            ->where('user_id', Auth::id())
                            ->orderBy('year', 'DESC')
                            ->orderBy('month', 'DESC')
                            ->paginate(10); 

        $payrolls = $payrolls->appends([ 
            'year' => $year
        ]);

        return view('accounts.payroll', compact('year', 'payrolls'));
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user();

        // Simpan jika belum ada
        $user->fcmTokens()->updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            ['device' => $request->header('User-Agent')]
        );

        return response()->json(['status' => 'success']);
    }

    public function removeFcmToken(Request $request)
    {

        $user = auth()->user(); 
        $user->fcmTokens()->delete();

        return response()->json(['status' => 'success']);
    }

}
