@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-semibold mb-4">Peta Jabatan</h1>

  <div id="orgchart" style="width:100%; height: 75vh; border: 1px solid #e5e7eb; border-radius: 6px;"></div>
</div>
@endsection

@once
  @push('scripts')
    {{-- Memuat bundle JS via Vite --}}
    @vite('resources/js/organogram.js')
  @endpush
@endonce