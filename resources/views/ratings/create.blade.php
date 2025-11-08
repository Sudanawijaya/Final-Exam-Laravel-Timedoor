@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-3xl font-light tracking-tight text-gray-900">Beri Rating Buku</h2>
            <p class="mt-2 text-sm text-gray-500">Bagikan pendapat Anda tentang buku favorit</p>
        </div>

        {{-- Error Messages --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-100">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-800 mb-2">Harap perbaiki kesalahan berikut:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)
                                <li class="text-sm text-red-700">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('ratings.store') }}" class="space-y-6">
            @csrf

            {{-- User Select --}}
            <div>
                <label for="user_id" class="block text-xs font-medium text-gray-700 mb-2">
                    Pengguna
                </label>
                <select id="user_id" name="user_id" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Pilih pengguna</option>
                    @foreach(\App\Models\User::limit(1000)->get() as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Author Select --}}
            <div>
                <label for="author" class="block text-xs font-medium text-gray-700 mb-2">
                    Penulis
                </label>
                <select id="author" name="author_id" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Pilih penulis</option>
                    @foreach($authors as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Book Select--}}
            <div>
                <label for="book" class="block text-xs font-medium text-gray-700 mb-2">
                    Buku
                </label>
                <select id="book" name="book_id" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Pilih penulis terlebih dahulu</option>
                </select>
            </div>

            {{-- Rating Select --}}
            <div>
                <label for="rating" class="block text-xs font-medium text-gray-700 mb-2">
                    Rating
                </label>
                <select id="rating" name="rating" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    @for($i=1;$i<=10;$i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i == 10 ? '★' : '' }}</option>
                    @endfor
                </select>
            </div>

            {{-- Review Textarea --}}
            <div>
                <label for="review" class="block text-xs font-medium text-gray-700 mb-2">
                    Ulasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="review" name="review" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm resize-none"
                          placeholder="Bagikan pemikiran Anda tentang buku ini..."></textarea>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit"
                        class="w-full px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Kirim Rating
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('author').addEventListener('change', function(){
        const aid = this.value;
        const sel = document.getElementById('book');
        sel.innerHTML = '<option>Memuat...</option>';
        if (!aid) {
             sel.innerHTML = '<option value="">Pilih penulis terlebih dahulu</option>';
             return;
        }

        fetch('/api/author/' + aid + '/books')
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(data => {
                sel.innerHTML = '<option value="">Pilih buku</option>';
                data.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.text = b.title;
                    sel.appendChild(opt);
                });
            })
            .catch(()=>{
                sel.innerHTML = '<option value="">Gagal memuat buku</option>';
            });
    });
</script>
@endsection