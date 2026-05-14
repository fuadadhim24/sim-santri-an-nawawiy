<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        @if($faqs->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-primary/10 rounded-full text-primary mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Informasi & FAQ</h3>
                            <p class="text-sm text-gray-500">Program, biaya, dan informasi penting lainnya</p>
                        </div>
                    </div>

                    @foreach($faqs as $category => $categoryFaqs)
                        @php
                            $categoryLabels = [
                                'program' => 'Info Program',
                                'biaya' => 'Informasi Biaya',
                                'fasilitas' => 'Fasilitas',
                                'pendaftaran' => 'Pendaftaran',
                                'umum' => 'Umum',
                                'pengumuman' => 'Pengumuman',
                            ];
                        @endphp
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-primary rounded-full mr-2"></span>
                                {{ $categoryLabels[$category] ?? ucfirst($category) }}
                            </h4>
                            <div class="space-y-3">
                                @foreach($categoryFaqs as $faq)
                                    <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                        <summary class="flex items-center justify-between p-4 cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <span class="font-medium text-gray-800 text-sm">{{ $faq->title }}</span>
                                            <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div class="p-4 border-t border-gray-200">
                                            <div class="prose prose-sm max-w-none text-gray-600">
                                                {!! nl2br(e($faq->content)) !!}
                                            </div>

                                            @if($faq->image_path)
                                                <div class="mt-4">
                                                    <img src="{{ asset('storage/' . $faq->image_path) }}" alt="{{ $faq->title }}"
                                                        class="rounded-lg border border-gray-200 max-w-full h-auto max-h-64 object-contain">
                                                </div>
                                            @endif

                                            @if($faq->pdf_path)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-700 mb-2">Dokumen Terlampir:</p>
                                                    <iframe src="{{ asset('storage/' . $faq->pdf_path) }}" 
                                                        class="w-full h-96 rounded-lg border border-gray-200"
                                                        title="{{ $faq->title }}"></iframe>
                                                    <a href="{{ asset('storage/' . $faq->pdf_path) }}" target="_blank"
                                                        class="inline-flex items-center mt-2 text-sm text-primary hover:text-primary/80 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                        Buka di tab baru
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                        <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada FAQ atau Informasi</h3>
                    <p class="text-gray-500">Saat ini belum ada informasi yang ditambahkan oleh admin pesantren.</p>
                </div>
            </div>
        @endif

    </div>
</div>
