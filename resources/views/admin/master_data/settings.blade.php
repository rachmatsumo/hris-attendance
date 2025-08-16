@extends('layouts.app')

@section('content') 
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                     {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        {{-- <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('location.store') }}" title="Tambah Area Kerja" data-id=""><i class="bi bi-plus"></i></a>                --}}
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Pengaturan Sistem</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">Pengaturan</th>
                                    <th class="v-middle">Nilai</th>
                                    <th class="v-middle">Description</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $a)
                                <tr>
                                    <td class="v-middle">{{ ucwords(str_replace('_',' ', $a->key)) }}</td>
                                    <td class="v-middle">{{ $a->value }}</td>
                                    <td class="v-middle">{{ $a->description }}</td> 
                                    <td>
                                          <button class="btn btn-sm btn-primary openModalInputBtn editDataBtn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInput"
                                                method="put"
                                                title="{{ ucwords(str_replace('_',' ', $a->key)) }}"
                                                data-id="{{ $a->id }}"
                                                data-url="{{ route('setting.update', $a->id) }}">
                                            Edit
                                        </button> 
                                    </td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> 
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="" method="POST" id="inputForm">
        <!-- CSRF token jika Laravel -->
        @csrf
        <div id="methodField"></div>
        <div class="modal-header">
          <h5 class="modal-title" id="modalInputLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">  
            <div class="mb-3" id="element_setting"></div> 
            <p class="mx-2" id="description"></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('editDataBtn')) {
            const id = e.target.dataset.id;
            const url = `/setting/${id}`; // route show bisa dikustom
            const form = document.getElementById('inputForm');
            const methodField = document.getElementById('methodField');

            
            fetch(url)
            .then(res => res.json())
            .then(data => {
              console.log(data);
              // Set action form dan method
                    form.action = e.target.dataset.url;
                    methodField.innerHTML = '@method("PUT")';

                    form.querySelector('#element_setting').innerHTML = data.element; 
                    form.querySelector('#description').innerText = data.description;


                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection