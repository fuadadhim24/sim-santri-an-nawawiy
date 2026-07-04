<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 bg-primary/10 rounded-full text-primary mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-foreground">Informasi & FAQ</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Panduan program pendidikan, rincian biaya, dan info administrasi pesantren.</p>
            </div>
        </div>
    </div>

    @if($faqs->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- FAQ Accordions (Spans 2 columns) -->
            <div class="lg:col-span-2 space-y-5">
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
                    <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden" style="margin-bottom: 1.25rem;">
                        <div class="px-5 py-4 border-b border-border bg-muted/20">
                            <h3 class="font-bold text-foreground text-sm flex items-center">
                                <span class="w-1.5 h-1.5 bg-primary rounded-full mr-2"></span>
                                {{ $categoryLabels[$category] ?? ucfirst($category) }}
                            </h3>
                        </div>
                        <div class="divide-y divide-border">
                            @foreach($categoryFaqs as $faq)
                                <details class="group">
                                    <summary class="flex items-center justify-between p-4 cursor-pointer hover:bg-muted/30 transition-colors">
                                        <span class="font-semibold text-foreground text-sm">{{ $faq->title }}</span>
                                        <svg class="w-4 h-4 text-muted-foreground transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </summary>
                                    <div class="p-4 bg-muted/10 border-t border-border">
                                        <div class="prose prose-sm max-w-none text-muted-foreground text-xs leading-relaxed">
                                            {!! nl2br(e($faq->content)) !!}
                                        </div>

                                        @if($faq->image_path)
                                            <div class="mt-3">
                                                <img src="{{ asset('storage/' . $faq->image_path) }}" alt="{{ $faq->title }}"
                                                    class="rounded-lg border border-border max-w-full h-auto max-h-64 object-contain">
                                            </div>
                                        @endif

                                        @if($faq->pdf_path)
                                            <div class="mt-3 border-t border-border/55 pt-3">
                                                <p class="text-xs font-semibold text-foreground mb-2">Dokumen Terlampir:</p>
                                                <iframe src="{{ asset('storage/' . $faq->pdf_path) }}" 
                                                    class="w-full h-80 rounded-lg border border-border"
                                                    title="{{ $faq->title }}"></iframe>
                                                <a href="{{ asset('storage/' . $faq->pdf_path) }}" target="_blank"
                                                    class="inline-flex items-center mt-2 text-xs text-primary hover:opacity-85 font-semibold">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    Buka dokumen di tab baru
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

            <!-- Help Contact Column -->
            <div class="lg:col-span-1">
                <div class="bg-card border border-border rounded-xl p-5 shadow-sm space-y-3">
                    <h4 class="font-bold text-foreground text-sm">Butuh Bantuan Lain?</h4>
                    <p class="text-xs text-muted-foreground leading-relaxed">
                        Jika Anda tidak menemukan jawaban atas pertanyaan Anda, silakan hubungi bagian administrasi pondok pesantren melalui WhatsApp.
                    </p>
                    <div class="pt-2">
                        <a href="https://wa.me/6280000000002" target="_blank"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary text-primary-foreground text-xs font-bold rounded-lg hover:opacity-90 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.504-5.726-1.464L0 24zm6.177-3.623l.362.215c1.545.918 3.25 1.4 5.006 1.401 5.485 0 9.948-4.414 9.951-9.84.002-2.628-1.018-5.1-2.87-6.956-1.854-1.857-4.322-2.88-6.95-2.882-5.49 0-9.953 4.417-9.956 9.846-.001 1.905.486 3.766 1.41 5.394l.236.413L2.613 21.43l3.621-1.053z" />
                            </svg>
                            Hubungi Admin TU
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-card border border-border rounded-xl shadow-sm p-12 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary/10 mb-4">
                <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-foreground mb-1">Belum Ada Informasi FAQ</h3>
            <p class="text-xs text-muted-foreground">Saat ini belum ada informasi yang ditambahkan oleh pengurus pesantren.</p>
        </div>
    @endif
</div>
