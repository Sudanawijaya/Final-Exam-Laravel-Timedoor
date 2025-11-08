@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-light tracking-tight text-gray-900">Penulis Teratas</h1>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-100 inline-flex gap-2">
        @php
            $currentTab = request('tab', 'popularity');
        @endphp

        @foreach (['popularity' => 'Popularitas', 'avg_rating' => 'Rating', 'trending' => 'Trending'] as $key => $label)
            <a href="{{ route('authors.top',['tab'=>$key]) }}"
               class="px-6 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                      @if($currentTab === $key) 
                          bg-gray-900 text-white
                      @else 
                          text-gray-600 hover:text-gray-900 hover:bg-gray-50
                      @endif">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-16">
                            No
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Nama Penulis
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            @if($currentTab=='popularity') 
                                Pemilih (>5 Rating)
                            @elseif($currentTab=='avg_rating') 
                                Rata-rata Rating
                            @else 
                                Skor Tren
                            @endif
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Total Rating
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Buku Terbaik
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Buku Terburuk
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($authors as $index => $a)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-sm font-medium text-gray-700">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $a->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($currentTab=='popularity')
                                    <span class="text-sm font-semibold text-gray-900">{{ $a->voters_gt5 ?? 0 }}</span>
                                @elseif($currentTab=='avg_rating')
                                    <span class="inline-flex items-center gap-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($a->avg_rating ?? 0, 2) }}</span>
                                        <span class="text-xs text-gray-500">/10</span>
                                    </span>
                                @else
                                    <span class="text-xs font-mono text-gray-600">{{ number_format($a->trend_score ?? 0, 6) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $stats[$a->id]['total'] ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $stats[$a->id]['best']->title ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $stats[$a->id]['worst']->title ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <p class="text-sm text-gray-500">Tidak ada penulis ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection