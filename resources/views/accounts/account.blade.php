@extends('layouts.app')

@section('content') 

    <div class="py-5 bg-gray-100 min-h-screen">

    
        
        <ul class="menu-list">
            <li>
                <a href="{{ route('account.edit') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-person-circle mb-0"></i></span> Perbarui Profile
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('account.change-password') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-key mb-0"></i></span> Ubah Kata Sandi
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            
            <li class="mt-5"> 
                <a class="logout" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                    <div>
                        <span class="mb-0"><i class="bi bi-door-open mb-0"></i></span> {{ __('Logout') }}
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
@endsection