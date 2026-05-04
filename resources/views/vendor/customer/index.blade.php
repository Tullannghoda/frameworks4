@extends('layouts.master')

@section('title', 'Data Customer')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Data Customer</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('customerdata.create-blob') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-camera-fill me-1"></i> Tambah (BLOB)
            </a>
            <a href="{{ route('customerdata.create-file') }}" class="btn btn-success btn-sm">
                <i class="bi bi-camera-fill me-1"></i> Tambah (File)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">#</th>
                            <th width="80">Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Tipe Foto</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $c->foto_url }}"
                                     alt="{{ $c->nama }}"
                                     class="rounded-circle"
                                     width="45" height="45"
                                     style="object-fit:cover;">
                            </td>
                            <td>{{ $c->nama }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->telepon ?? '-' }}</td>
                            <td>
                                @if($c->foto_blob)
                                    <span class="badge bg-primary">BLOB</span>
                                @elseif($c->foto_path)
                                    <span class="badge bg-success">File</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('customerdata.destroy', $c->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus customer ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data customer.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $customers->links() }}
    </div>
    @endif

</div>
@endsection
