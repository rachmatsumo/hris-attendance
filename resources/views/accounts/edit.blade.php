@extends('layouts.app')

@section('content') 
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                 <div class="card-body">


                    {{-- Tombol Back di atas --}}
                    <div class="mb-3">
                        <a href="{{ route('account.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>

                    {{-- Avatar Bulat --}}
                    <div class="mb-4 text-center">
                        <img 
                            src="{{ @Auth::user()->profile_photo 
                                    ? asset('upload/avatar/' . @Auth::user()->profile_photo) 
                                    : asset('upload/avatar/default.png') }}" 
                            alt="Avatar" 
                            class="p-2 rounded-circle border" 
                            style="width: 120px; height: 120px; object-fit: cover;object-position: top; /"
                            id="avatarPreview">

                        <div class="mt-2">
                            <label for="avatar" class="btn btn-sm btn-outline-primary">
                                Ubah Foto
                            </label>
                        </div>
                    </div>

                    {{-- Form Update Profile --}}
                    <form method="post" action="{{ route('account.update', Auth::id()) }}" enctype="multipart/form-data">

                        @csrf
                        @method('patch')

                         {{-- Input foto disembunyikan --}}
                        <input type="file" 
                               id="avatar" 
                               name="avatar" 
                               accept="image/*" 
                               class="d-none"
                               onchange="previewAvatar(event)">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <p class="text-muted small mt-2">
                                    Email belum diverifikasi.
                                    <button form="send-verification" class="btn btn-link btn-sm p-0">
                                        Kirim ulang verifikasi
                                    </button>
                                </p>
                            @endif
                        </div>

                        {{-- Tombol Back & Save di bawah --}}
                        <div class="d-flex justify-content-end mt-4">
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a> --}}
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(event) {
    const output = document.getElementById('avatarPreview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = () => URL.revokeObjectURL(output.src);
};
</script>
@endsection
