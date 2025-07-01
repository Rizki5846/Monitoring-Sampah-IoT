<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-3">Kelola Jadwal Pengangkutan</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('jadwal.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <select name="hari" class="form-select">
                    <option disabled selected>Pilih hari</option>
                    @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Tuesday'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $j)
                <tr>
                    <td>{{ $j->hari }}</td>
                    <td>
                        <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
