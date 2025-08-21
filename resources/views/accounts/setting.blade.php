@extends('layouts.app')

@section('content') 
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Header & Filter -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('account.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Pengaturan</h6>
                    </div> 

                    <div class="row">
                        <div class="col-12 col-md-12 mb-2 d-flex justify-content-between align-items-center py-2">
                            <label>Notifikasi</label>
                            <label class="switch">
                                <input type="checkbox" id="notifSwitch">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection
