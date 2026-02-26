<div>
    <x-slot name="header">
        Terbitkan Tagihan Massal
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-foreground">Pembuatan Tagihan Massal</h3>
                <p class="text-sm text-muted-foreground">
                    Fitur ini akan memindai data santri dan menerbitkan tagihan secara otomatis berdasarkan aturan di
                    Master Biaya (Interval Sekali, Bulanan, atau Tahunan).
                    Sistem akan otomatis melewati jika tagihan sudah pernah dibuat untuk mencegah duplikasi.
                </p>
            </div>

            <form wire:submit="generate" class="space-y-6">

                <div class="space-y-4">
                    <label class="block text-sm font-medium text-foreground">Pilih Jenis Tagihan yang
                        Diterbitkan:</label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label
                            class="relative flex items-start p-4 border border-input bg-background rounded-lg cursor-pointer hover:bg-muted/50">
                            <div class="flex items-center h-5">
                                <input wire:model.live="genOnce" type="checkbox"
                                    class="h-4 w-4 text-primary border-input rounded focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-foreground text-xs uppercase tracking-wider block">Sekali
                                    Saja</span>
                                <span class="text-muted-foreground text-xs">Pendaftaran, dll</span>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 border border-input bg-background rounded-lg cursor-pointer hover:bg-muted/50">
                            <div class="flex items-center h-5">
                                <input wire:model.live="genMonthly" type="checkbox"
                                    class="h-4 w-4 text-primary border-input rounded focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span
                                    class="font-medium text-foreground text-xs uppercase tracking-wider block">Bulanan</span>
                                <span class="text-muted-foreground text-xs">SPP, Makan, dll</span>
                            </div>
                        </label>

                        <label
                            class="relative flex items-start p-4 border border-input bg-background rounded-lg cursor-pointer hover:bg-muted/50">
                            <div class="flex items-center h-5">
                                <input wire:model.live="genYearly" type="checkbox"
                                    class="h-4 w-4 text-primary border-input rounded focus:ring-ring">
                            </div>
                            <div class="ml-3 text-sm">
                                <span
                                    class="font-medium text-foreground text-xs uppercase tracking-wider block">Tahunan</span>
                                <span class="text-muted-foreground text-xs">Daftar Ulang, dll</span>
                            </div>
                        </label>
                    </div>

                    @if ($genMonthly)
                        <div class="mt-4 p-4 bg-primary/5 border border-primary/20 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-primary">Filter Jatuh Tempo</h4>
                                    <p class="text-xs text-muted-foreground">Hanya terbitkan tagihan bulanan untuk
                                        santri yang jadwal penagihannya jatuh pada hari ini (Tanggal
                                        {{ date('j') }}).</p>
                                </div>
                                <div class="ml-4">
                                    <button type="button"
                                        wire:click="$set('onlyDue', {{ $onlyDue ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 {{ $onlyDue ? 'bg-primary' : 'bg-gray-200' }}">
                                        <span class="sr-only">Toggle Jatuh Tempo</span>
                                        <span aria-hidden="true"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $onlyDue ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($genMonthly || $genYearly)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-muted/30 p-4 rounded-lg border border-border">
                        <!-- Month -->
                        @if ($genMonthly)
                            <div>
                                <label for="month" class="block text-sm font-medium text-foreground">Bulan
                                    Tagihan</label>
                                <select wire:model="month" id="month"
                                    class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}">
                                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <span class="text-destructive text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Year -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-foreground">Tahun
                                Tagihan</label>
                            <input wire:model="year" type="number" id="year"
                                class="mt-1 block w-full px-3 py-2 border border-input bg-background rounded-md shadow-sm focus:outline-none focus:ring-ring focus:border-ring sm:text-sm">
                            @error('year')
                                <span class="text-destructive text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.billings') }}"
                        class="px-4 py-2 border border-input rounded-md shadow-sm text-sm font-medium text-foreground bg-background hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                        Buat Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
