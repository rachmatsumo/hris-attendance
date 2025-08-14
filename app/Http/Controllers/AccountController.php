<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View; 
use Imagick;

class AccountController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): View
    {
        return view('accounts.account', [
            'user' => $request->user(),
        ]);
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

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

       $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // boleh kosong
        ]);

        // Update nama/email
        $user->name = $request->name;
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null; // reset verifikasi email
        }

        $file = $request->file('avatar');
        $filename  = time() . '.jpg';
        $avatarDir = public_path('upload/avatar');
        $thumbDir  = public_path('upload/avatar/thumbnails');

        if (!file_exists($avatarDir)) mkdir($avatarDir, 0777, true);
        if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);

        // Hapus avatar lama
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
        $file = $request->file('avatar');

        // Hapus avatar lama hanya jika bukan default
        if ($user->profile_photo && $user->profile_photo !== 'default.png' &&
            file_exists($avatarDir . '/' . $user->profile_photo)) {
            @unlink($avatarDir . '/' . $user->profile_photo);
        }
        if ($user->profile_photo && $user->profile_photo !== 'default.png' &&
            file_exists($thumbDir . '/' . $user->profile_photo)) {
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
            return redirect()->back()->with('error', 'Imagick gagal proses gambar: ' . $e->getMessage());
        }

        $user->profile_photo = $filename;
    }

        $user->save();

        // return redirect()->route('account.edit', $user->id)->with('status', 'profile-updated');
        return back()->with('status', 'profile-updated');

    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
