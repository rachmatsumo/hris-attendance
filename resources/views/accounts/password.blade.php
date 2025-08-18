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

                    {{-- Form Ubah Password --}}
                    <form method="post" action="{{ route('password.update.custom') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label">
                                Kata Sandi Lama
                            </label>
                            <input id="update_password_current_password" 
                                   name="current_password" 
                                   type="password" 
                                   class="form-control" 
                                   autocomplete="current-password">
                            @if ($errors->updatePassword->has('current_password'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('current_password') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password" class="form-label">
                                Kata Sandi Baru
                            </label>
                            <input id="update_password_password" 
                                   name="password" 
                                   type="password" 
                                   class="form-control" 
                                   autocomplete="new-password">
                            @if ($errors->updatePassword->has('password'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="update_password_password_confirmation" class="form-label">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input id="update_password_password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   class="form-control" 
                                   autocomplete="new-password">
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <div class="text-danger small mt-1">
                                    {{ $errors->updatePassword->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Save & Back --}}
                        <div class="d-flex justify-content-end">
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">
                                ← Kembali
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
@endsection
