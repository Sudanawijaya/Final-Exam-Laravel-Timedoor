@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-light tracking-tight text-gray-900">Daftar Buku</h1>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('books.index') }}" class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        {{-- Menggunakan grid-cols-4. Asumsi layout Anda tetap ingin 4 kolom. --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm"
                    placeholder="Judul, ISBN, penulis...">
            </div>

            {{-- Author --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Penulis</label>
                <select name="author_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Semua Penulis</option>
                    @foreach($authors as $a)
                        <option value="{{ $a->id }}" @selected(request('author_id')==$a->id)>
                            {{ $a->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category (MULTIPLE SELECTION) --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Kategori</label>
                <select name="categories[]" multiple 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm h-28">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(in_array($c->id, request('categories') ?? []))>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Mode Kategori (AND/OR Logic) --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Mode Kategori</label>
                <select name="cat_mode" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="OR" @selected(request('cat_mode', 'OR') == 'OR')>OR (Salah Satu)</option>
                    <option value="AND" @selected(request('cat_mode') == 'AND')>AND (Semua)</option>
                </select>
            </div>

            {{-- Year From --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Tahun Dari</label>
                <input type="number" name="year_from" value="{{ request('year_from') }}" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
            </div>

            {{-- Year To --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Tahun Sampai</label>
                <input type="number" name="year_to" value="{{ request('year_to') }}" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
            </div>

            {{-- Availability --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Ketersediaan</label>
                <select name="availability" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Semua</option>
                    <option value="available" @selected(request('availability')=='available')>Tersedia</option>
                    <option value="rented" @selected(request('availability')=='rented')>Dipinjam</option>
                    <option value="reserved" @selected(request('availability')=='reserved')>Direservasi</option>
                </select>
            </div>

            {{-- Store Location --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Lokasi Toko</label>
                <select name="store_location" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="">Semua Toko</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" @selected(request('store_location')==$loc)>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rating Min --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Rating Minimal</label>
                <input type="number" name="rating_min" min="1" max="10" value="{{ request('rating_min') }}" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
            </div>

            {{-- Rating Max --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Rating Maksimal</label>
                <input type="number" name="rating_max" min="1" max="10" value="{{ request('rating_max') }}" 
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
            </div>

            {{-- Sort --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Urutkan</label>
                <select name="sort" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-gray-400 focus:ring-0 transition-colors text-sm">
                    <option value="weighted" @selected($sort=='weighted')>Skor Tertimbang</option>
                    <option value="votes" @selected($sort=='votes')>Jumlah Vote</option>
                    <option value="recent_pop" @selected($sort=='recent_pop')>Populer Terkini (30 Hari)</option>
                    <option value="alpha" @selected($sort=='alpha')>Alfabetis</option>
                </select>
            </div>

            {{-- CATATAN: HIDDEN INPUT cat_mode LAMA SUDAH DIHAPUS DARI SINI --}}

        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
            <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                Terapkan Filter
            </button>
            <a href="{{ route('books.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Reset
            </a>
        </div>
    </form>

    {{-- Pagination Info (Top) --}}
    <div class="flex items-center justify-between px-1">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">{{ $books->firstItem() ?? 0 }}</span> 
            sampai <span class="font-medium">{{ $books->lastItem() ?? 0 }}</span> 
            dari <span class="font-medium">{{ $books->total() }}</span> buku
            <span class="text-gray-400 mx-2">•</span>
            <span class="font-medium">Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}</span>
        </p>
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ISBN</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Skor</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Vote</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Tren</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Toko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $book->title }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $book->isbn ?? '-' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $book->author->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $book->categories->pluck('name')->join(', ') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $book->publication_year ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format((float)$book->weighted_score, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ number_format((float)$book->avg_rating, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $book->ratings_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($book->trend >= 'up')
                                    <span class="text-green-600 text-lg">↑</span>
                                @elseif($book->trend <= 'down')
                                    <span class="text-red-600 text-lg">↓</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
                                    {{ $book->availability === 'available' ? 'bg-green-50 text-green-700' : 
                                        ($book->availability === 'rented' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $book->availability === 'available' ? 'Tersedia' : 
                                        ($book->availability === 'rented' ? 'Dipinjam' : 'Direservasi') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $book->store_location }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <p class="text-sm text-gray-500">Tidak ada buku ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">
            Halaman <span class="font-medium">{{ $books->currentPage() }}</span> dari <span class="font-medium">{{ $books->lastPage() }}</span>
        </p>
        <div>
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection