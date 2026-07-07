<div>
    <x-slot name="header">
        Manajemen Rombongan Belajar
    </x-slot>

    <div class="bg-card rounded-lg shadow-sm border border-border">
        <!-- Header Actions -->
        <div class="p-6 border-b border-border flex items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-card-foreground">Peta Rombongan Belajar</h3>
                <p class="text-sm text-muted-foreground mt-1">Kelola penempatan santri dalam kelas dan rombongan belajar secara interaktif.</p>
            </div>
            <div class="flex space-x-2 flex-nowrap">
                <button type="button" wire:click="openClassLevelModal" class="inline-flex items-center justify-center py-2 px-4 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 font-semibold whitespace-nowrap flex-none shrink-0 transition shadow-sm">
                    + Tingkat Kelas
                </button>
                <button type="button" wire:click="openPromotionWizard" class="inline-flex items-center justify-center py-2 px-4 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-semibold whitespace-nowrap flex-none shrink-0 transition shadow-sm">
                    Kenaikan & Kelulusan Massal
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="p-6 bg-background rounded-b-lg">
            <div class="flex overflow-x-auto space-x-4 pb-4 min-h-[70vh]">
                


                <!-- Columns: Class Levels -->
                @foreach($classLevels as $level)
                <div class="flex-shrink-0 w-80 bg-secondary/30 rounded-lg shadow-sm border border-border flex flex-col">
                    <div class="p-4 border-b border-border bg-card rounded-t-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-card-foreground">{{ $level->name }}</h3>
                            <p class="text-xs text-muted-foreground mt-1">{{ $level->studyGroups->sum('students_count') + $unassignedToRombel[$level->id]->count() }} Total Santri</p>
                        </div>
                        <button class="text-muted-foreground hover:text-foreground">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-3 flex-1 overflow-y-auto space-y-3">
                        
                        <!-- Card: Belum Ada Rombel (For this specific class level) -->
                        @if($unassignedToRombel[$level->id]->count() > 0)
                        <div wire:click="openManageStudentsModal('unassigned_level', {{ $level->id }}, 'Menunggu Rombel ({{ $level->name }})')" class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded shadow-sm border border-yellow-200 dark:border-yellow-700/50 cursor-pointer hover:border-yellow-400 transition">
                            <div class="flex justify-between items-center mb-1">
                                <h4 class="font-semibold text-sm text-yellow-800 dark:text-yellow-500">Menunggu Rombel</h4>
                                <span class="bg-yellow-200 dark:bg-yellow-800/50 text-yellow-800 dark:text-yellow-400 text-xs px-2 py-0.5 rounded-full">{{ $unassignedToRombel[$level->id]->count() }}</span>
                            </div>
                            <p class="text-xs text-yellow-600 dark:text-yellow-600/80">Santri ini sudah masuk {{ $level->name }} tapi belum punya kelas/ruangan.</p>
                        </div>
                        @endif

                        <!-- Cards: Study Groups (Rombel) -->
                        @foreach($level->studyGroups as $rombel)
                        <div wire:click="openManageStudentsModal('rombel', {{ $rombel->id }}, '{{ $rombel->name }}')" class="bg-card p-3 rounded shadow-sm border border-border cursor-pointer hover:border-primary transition group relative">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-bold text-sm text-card-foreground">{{ $rombel->name }}</h4>
                                <button wire:click.stop="openStudyGroupModal({{ $level->id }}, {{ $rombel->id }})" type="button" class="text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                            
                            <div class="flex items-end justify-between">
                                <div>
                                    <div class="text-2xl font-bold {{ $rombel->students_count >= $rombel->max_capacity ? 'text-destructive' : 'text-primary' }}">
                                        {{ $rombel->students_count }}
                                    </div>
                                    <p class="text-xs text-muted-foreground mt-1">Kapasitas: {{ $rombel->max_capacity }}</p>
                                </div>
                                
                                <!-- Progress Bar Capacity -->
                                @php
                                    $percentage = ($rombel->max_capacity > 0) ? min(100, round(($rombel->students_count / $rombel->max_capacity) * 100)) : 0;
                                    $color = $percentage >= 100 ? 'bg-destructive' : ($percentage >= 80 ? 'bg-yellow-400' : 'bg-emerald-500');
                                @endphp
                                <div class="w-16 h-1.5 bg-secondary rounded-full overflow-hidden">
                                    <div class="h-full {{ $color }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Add Rombel Button for this Level -->
                        <button wire:click="openStudyGroupModal({{ $level->id }})" class="w-full py-2 border-2 border-dashed border-border rounded text-muted-foreground text-sm font-semibold hover:border-primary hover:text-foreground transition">
                            + Tambah Rombel
                        </button>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Modal Class Level -->
    @if($showClassLevelModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="saveClassLevel">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            {{ $classLevelId ? 'Ubah Tingkat Kelas' : 'Tambah Tingkat Kelas' }}
                        </h3>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tingkat (Contoh: Kelas 1, SMP VII)</label>
                            <input wire:model="classLevelName" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('classLevelName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Urutan (Untuk sorting kolom)</label>
                            <input wire:model="classLevelOrder" type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('classLevelOrder') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveClassLevel" class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <svg wire:loading wire:target="saveClassLevel" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="saveClassLevel">Simpan</span>
                            <span wire:loading wire:target="saveClassLevel">Simpan...</span>
                        </button>
                        <button type="button" wire:click="$set('showClassLevelModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Study Group -->
    @if($showStudyGroupModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="saveStudyGroup">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            {{ $studyGroupId ? 'Ubah Rombel' : 'Tambah Rombel' }}
                        </h3>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Rombel (Contoh: 1-A, Abu Bakar)</label>
                            <input wire:model="studyGroupName" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('studyGroupName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kapasitas Ruangan</label>
                            <input wire:model="studyGroupCapacity" type="number" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('studyGroupCapacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveStudyGroup" class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <svg wire:loading wire:target="saveStudyGroup" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="saveStudyGroup">Simpan</span>
                            <span wire:loading wire:target="saveStudyGroup">Simpan...</span>
                        </button>
                        <button type="button" wire:click="$set('showStudyGroupModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Manage Students -->
    @if($showManageStudentsModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showManageStudentsModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-4xl" style="max-height: 90vh;">
                <div class="flex flex-col h-full" style="max-height: 90vh;">
                    <!-- Modal Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center rounded-t-lg">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Penempatan Santri: {{ $manageSourceName }}
                        </h3>
                        <button wire:click="$set('showManageStudentsModal', false)" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div x-data="{ selectedIds: @entangle('selectedStudentIds') }" class="px-6 py-4 flex-1 overflow-y-auto bg-white">
                        <div class="flex justify-between items-center mb-4">
                            <button type="button" 
                                    @click="selectedIds.length === {{ count($modalStudents) }} && {{ count($modalStudents) }} > 0 ? selectedIds = [] : selectedIds = [{{ implode(',', $modalStudents->pluck('id')->toArray()) }}].map(String)" 
                                    class="text-sm font-semibold text-primary hover:text-primary/80">
                                <span x-text="selectedIds.length === {{ count($modalStudents) }} && {{ count($modalStudents) }} > 0 ? 'Deselect All' : 'Select All'">Select All</span>
                                ({{ count($modalStudents) }} Santri)
                            </button>
                            <div class="text-sm text-gray-500">
                                Terpilih: <span class="font-bold text-primary" x-text="selectedIds.length">0</span>
                            </div>
                        </div>

                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                            Pilih
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Santri
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIS / Unit
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($modalStudents as $student)
                                    <tr class="hover:bg-gray-50 cursor-pointer" @click="document.getElementById('checkbox-{{ $student->id }}').click()">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input id="checkbox-{{ $student->id }}" x-model="selectedIds" value="{{ $student->id }}" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" @click.stop>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $student->nis ?? '-' }} / {{ $student->unit_code }}</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada santri di kelompok ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- BILLING TRANSITION SECTION FOR MOVE -->
                        @if($targetMoveId && !$this->isSameClassLevel())
                            <div class="mt-6 border-t pt-4 space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900">Penyesuaian Tagihan Kelas Baru</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 uppercase">Kebijakan Tagihan Lama (Unpaid):</label>
                                        <div class="space-y-2 mt-1.5">
                                            <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none">
                                                <input type="radio" wire:model.live="moveBillingPolicy" value="none" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                <div>
                                                    <span class="font-semibold">Jangan Ubah Tagihan Lama</span>
                                                    <p class="text-[10px] text-gray-500 mt-0.5">Semua tagihan lama tetap dibiarkan aktif.</p>
                                                </div>
                                            </label>

                                            <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none mt-2">
                                                <input type="radio" wire:model.live="moveBillingPolicy" value="delete_all_and_new" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                <div>
                                                    <span class="font-semibold text-destructive">Hapus Semua Tagihan Lama</span>
                                                    <p class="text-[10px] text-gray-500 mt-0.5">Semua tagihan kelas lama yang belum dibayar akan dihapus.</p>
                                                </div>
                                            </label>

                                            <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none mt-2">
                                                <input type="radio" wire:model.live="moveBillingPolicy" value="delete_except_month_and_new" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                <div>
                                                    <span class="font-semibold text-primary">Hapus Kecuali Bulan Ini</span>
                                                    <p class="text-[10px] text-gray-500 mt-0.5">Hapus tagihan lama kecuali tagihan yang dibuat/jatuh tempo bulan ini.</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-2 relative">
                                        <label class="block text-xs font-semibold text-gray-700 uppercase">Buat Tagihan Kelas Baru (Jika ada):</label>
                                        
                                        <!-- Loading state -->
                                        <div wire:loading wire:target="targetMoveId" class="w-full mt-1.5 animate-pulse">
                                            <div class="flex items-center gap-3 p-3 bg-blue-50/50 border border-blue-200 rounded-lg text-xs text-blue-800">
                                                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <span class="font-bold block">Menganalisis Tagihan Kelas...</span>
                                                    <span class="text-[10px] text-blue-600 block mt-0.5">Memeriksa struktur biaya dan kebijakan transisi tingkat kelas tujuan...</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content state -->
                                        <div wire:loading.remove wire:target="targetMoveId">
                                            @if(empty($availableMoveBillings))
                                                <p class="text-xs text-gray-500 bg-gray-50 p-2.5 rounded border border-dashed mt-1.5">Tidak ada tagihan khusus untuk tingkat kelas tujuan.</p>
                                            @else
                                                <div class="space-y-2 mt-1.5 max-h-[120px] overflow-y-auto p-1.5 border rounded">
                                                    @foreach($availableMoveBillings as $billing)
                                                        <label class="flex items-center text-xs text-gray-700 cursor-pointer select-none">
                                                            <input type="checkbox" wire:model.live="moveBillingCategories" value="{{ $billing['id'] }}" class="rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                            <div class="flex-1 flex justify-between pr-2">
                                                                <span>{{ $billing['name'] }}</span>
                                                                <span class="font-mono text-gray-500">Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}</span>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer & Action -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="w-full sm:w-1/2 flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Pindahkan ke:</label>
                            <select wire:model.live="targetMoveId" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md shadow-sm">
                                <option value="">-- Pilih Tujuan --</option>
                                @foreach($classLevels as $level)
                                    <optgroup label="{{ $level->name }}">
                                        <option value="unassigned_level_{{ $level->id }}">📌 Belum Penempatan (Tetap di {{ $level->name }})</option>
                                        @foreach($level->studyGroups as $rombel)
                                            <option value="rombel_{{ $rombel->id }}">👉 Rombel: {{ $rombel->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        
                        <button wire:click="moveSelectedStudents" 
                                wire:loading.attr="disabled" 
                                type="button" 
                                class="w-full sm:w-auto inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-primary text-primary-foreground hover:bg-primary/90 focus:outline-none sm:text-sm"
                                :class="selectedIds.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                :disabled="selectedIds.length === 0">
                            <svg wire:loading wire:target="moveSelectedStudents" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="moveSelectedStudents">Pindahkan Santri</span>
                            <span wire:loading wire:target="moveSelectedStudents">Memindahkan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Wizard Kenaikan Kelas Massal -->
    @if($showPromotionWizard)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showPromotionWizard', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-5xl" style="max-height: 90vh;">
                <div class="flex flex-col h-full" style="max-height: 90vh;">
                    <!-- Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center rounded-t-lg">
                        <h3 class="text-lg leading-6 font-bold text-gray-900">Wizard Kenaikan & Kelulusan Massal</h3>
                        <button wire:click="$set('showPromotionWizard', false)" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 flex-1 overflow-y-auto bg-white">
                        <!-- Stepper UI -->
                        <div class="flex items-center justify-between mb-8 max-w-xl mx-auto">
                            <div class="flex-1 text-center">
                                <div class="w-8 h-8 mx-auto bg-primary text-primary-foreground rounded-full flex items-center justify-center font-bold">1</div>
                                <div class="text-xs font-semibold mt-2 text-primary">Target</div>
                            </div>
                            
                            @if($destinationLevelId !== 'lulus')
                                <div class="flex-1 h-1 bg-gray-200"><div class="h-full bg-primary transition-all duration-300" style="width: {{ $wizardStep >= 2 ? '100%' : '0%' }}"></div></div>
                                <div class="flex-1 text-center">
                                    <div class="w-8 h-8 mx-auto {{ $wizardStep >= 2 ? 'bg-primary text-primary-foreground' : 'bg-gray-200 text-gray-500' }} rounded-full flex items-center justify-center font-bold transition-colors duration-300">2</div>
                                    <div class="text-xs font-semibold mt-2 {{ $wizardStep >= 2 ? 'text-primary' : 'text-gray-400' }}">Metode</div>
                                </div>
                                <div class="flex-1 h-1 bg-gray-200"><div class="h-full bg-primary transition-all duration-300" style="width: {{ $wizardStep >= 3 ? '100%' : '0%' }}"></div></div>
                                <div class="flex-1 text-center">
                                    <div class="w-8 h-8 mx-auto {{ $wizardStep >= 3 ? 'bg-primary text-primary-foreground' : 'bg-gray-200 text-gray-500' }} rounded-full flex items-center justify-center font-bold transition-colors duration-300">3</div>
                                    <div class="text-xs font-semibold mt-2 {{ $wizardStep >= 3 ? 'text-primary' : 'text-gray-400' }}">Review</div>
                                </div>
                            @else
                                <div class="flex-1 h-1 bg-gray-200"><div class="h-full bg-primary transition-all duration-300" style="width: {{ $wizardStep >= 3 ? '100%' : '0%' }}"></div></div>
                                <div class="flex-1 text-center">
                                    <div class="w-8 h-8 mx-auto {{ $wizardStep >= 3 ? 'bg-primary text-primary-foreground' : 'bg-gray-200 text-gray-500' }} rounded-full flex items-center justify-center font-bold transition-colors duration-300">2</div>
                                    <div class="text-xs font-semibold mt-2 {{ $wizardStep >= 3 ? 'text-primary' : 'text-gray-400' }}">Review & Kelulusan</div>
                                </div>
                            @endif
                        </div>

                        @if($wizardStep === 1)
                            <div class="space-y-6 max-w-lg mx-auto">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pilih Tingkat Kelas Asal</label>
                                    <select wire:model="sourceLevelId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md shadow-sm">
                                        <option value="">-- Pilih Kelas Asal --</option>
                                        @foreach($classLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sourceLevelId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="flex justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pilih Tingkat Kelas Tujuan</label>
                                    <select wire:model="destinationLevelId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md shadow-sm">
                                        <option value="">-- Pilih Kelas Tujuan --</option>
                                        @foreach($classLevels as $level)
                                            <option value="{{ $level->id }}">Naik ke: {{ $level->name }}</option>
                                        @endforeach
                                        <option value="lulus" class="text-green-600 font-bold">🎓 LULUS / ALUMNI</option>
                                    </select>
                                    @error('destinationLevelId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @elseif($wizardStep === 2)
                            <div class="space-y-4 max-w-2xl mx-auto">
                                <h4 class="font-bold text-gray-800 text-center mb-4">Pilih Metode Penempatan Rombel</h4>
                                
                                <!-- Opsi Paralel -->
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ $promotionMethod === 'paralel' ? 'border-primary ring-2 ring-primary' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="promotionMethod" value="paralel" class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Paralel (Sesuai Rombel Asal)</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Santri di 1-A akan otomatis dipindah ke 2-A. Jika tidak ada rombel tujuan dengan nama yang sama, santri akan ditaruh di "Belum Penempatan".</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 {{ $promotionMethod === 'paralel' ? 'text-primary' : 'hidden' }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                </label>

                                <!-- Opsi Acak -->
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ $promotionMethod === 'acak' ? 'border-primary ring-2 ring-primary' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="promotionMethod" value="acak" class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Rolling Acak (Shuffle)</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Sistem akan mengacak penempatan santri ke semua rombel di tingkat tujuan secara merata sesuai kapasitas ruangan.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 {{ $promotionMethod === 'acak' ? 'text-primary' : 'hidden' }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                </label>

                                <!-- Opsi Kosong -->
                                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ $promotionMethod === 'kosong' ? 'border-primary ring-2 ring-primary' : 'border-gray-300' }}">
                                    <input type="radio" wire:model.live="promotionMethod" value="kosong" class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Kosongkan Rombel</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Semua santri akan naik kelas, tapi rombel-nya dikosongkan (Masuk ke status Belum Penempatan). Rombel bisa Anda atur manual nanti.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 {{ $promotionMethod === 'kosong' ? 'text-primary' : 'hidden' }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                </label>

                                @if($promotionMethod === 'paralel' && $destinationLevelId !== 'lulus')
                                    <div class="mt-6 border-t border-gray-200 pt-6">
                                        <h4 class="font-bold text-gray-800 mb-3">Konfirmasi Pemetaan Rombel Paralel</h4>
                                        <p class="text-sm text-gray-500 mb-4">Sistem telah menebak otomatis tujuan rombel. Silakan sesuaikan jika ada yang kurang tepat.</p>
                                        
                                        <div class="space-y-3">
                                            @forelse($sourceStudyGroups as $sourceRombel)
                                                <div class="flex items-center gap-4 bg-gray-50 p-3 rounded border border-gray-200">
                                                    <div class="w-1/3 font-semibold text-gray-700 text-sm">
                                                        Dari: {{ $sourceRombel['name'] }}
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <select wire:model="paralelMapping.{{ $sourceRombel['id'] }}" class="block w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary">
                                                            <option value="">-- Belum Penempatan --</option>
                                                            @foreach($destinationStudyGroups as $destRombel)
                                                                <option value="{{ $destRombel['id'] }}">Ke: {{ $destRombel['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-sm text-gray-500">Tidak ada rombel di tingkat kelas asal.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($wizardStep === 3)
                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">
                                    {{ $destinationLevelId === 'lulus' ? 'Review Prediksi Kelulusan' : 'Review Prediksi Penempatan' }}
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ $destinationLevelId === 'lulus' 
                                        ? 'Periksa daftar santri yang akan diluluskan. Jika ada santri yang tidak lulus (tinggal/tetap di kelas lama), silakan centang/ceklis pada kotak merah "Abaikan" di sebelahnya.' 
                                        : 'Periksa hasil prediksi sistem. Jika ada santri yang tinggal kelas, silakan centang/ceklis pada kotak merah "Abaikan" di sebelahnya.' }}
                                </p>
                                
                                <div class="border rounded-lg overflow-hidden max-h-[50vh] overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Santri</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rombel Lama</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prediksi Rombel Baru</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-red-500 uppercase bg-red-50">❌ Abaikan (Tinggal Kelas)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($promotionDraft as $studentId => $data)
                                            <tr class="{{ $data['skip'] ? 'bg-red-50 opacity-50' : '' }}">
                                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $data['name'] }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $data['old_rombel'] }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($destinationLevelId === 'lulus')
                                                        <span class="text-green-600 font-bold">🎓 LULUS</span>
                                                    @else
                                                        <select wire:model="promotionDraft.{{ $studentId }}.new_rombel_id" class="text-sm border-gray-300 rounded focus:ring-primary focus:border-primary" {{ $data['skip'] ? 'disabled' : '' }}>
                                                            <option value="">Belum Penempatan</option>
                                                            @foreach($destinationStudyGroups as $dr)
                                                                <option value="{{ $dr['id'] }}">{{ $dr['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <input wire:model.live="promotionDraft.{{ $studentId }}.skip" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada santri di kelas asal.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- BILLING TRANSITION SECTION FOR WIZARD -->
                                <div class="mt-6 border-t pt-4 space-y-4">
                                    <h4 class="text-sm font-semibold text-gray-900">
                                        {{ $destinationLevelId === 'lulus' ? 'Kelola Sisa Tagihan Kelulusan' : 'Penyesuaian Tagihan Kelas Baru Massal' }}
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-semibold text-gray-700 uppercase">Kebijakan Tagihan Lama (Unpaid):</label>
                                            @if($destinationLevelId === 'lulus')
                                                <div class="space-y-2 mt-1.5">
                                                    <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none">
                                                        <input type="radio" wire:model.live="wizardBillingPolicy" value="graduation_keep_unpaid" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        <div>
                                                            <span class="font-semibold">Pertahankan Sisa Tagihan Lulusan</span>
                                                            <p class="text-[10px] text-gray-500 mt-0.5">Sisa tagihan belum dibayar tetap ditagihkan (wajib dilunasi sebelum keluar).</p>
                                                        </div>
                                                    </label>

                                                    <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none mt-2">
                                                        <input type="radio" wire:model.live="wizardBillingPolicy" value="graduation_delete_unpaid" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        <div>
                                                            <span class="font-semibold text-destructive">Hapus Sisa Tagihan (Pemutihan Utang)</span>
                                                            <p class="text-[10px] text-gray-500 mt-0.5">Semua sisa tagihan santri yang lulus akan dihapus/diarsipkan.</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            @else
                                                <div class="space-y-2 mt-1.5">
                                                    <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none">
                                                        <input type="radio" wire:model.live="wizardBillingPolicy" value="none" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        <div>
                                                            <span class="font-semibold">Jangan Ubah Tagihan Lama & Jangan Buat Baru</span>
                                                            <p class="text-[10px] text-gray-500 mt-0.5">Semua tagihan lama tetap aktif tanpa ada pembuatan tagihan otomatis baru.</p>
                                                        </div>
                                                    </label>

                                                    <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none mt-2">
                                                        <input type="radio" wire:model.live="wizardBillingPolicy" value="delete_all" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        <div>
                                                            <span class="font-semibold text-destructive">Hapus Semua Tagihan Lama & Buat Baru</span>
                                                            <p class="text-[10px] text-gray-500 mt-0.5">Hapus semua tagihan kelas lama (unpaid) dan buat tagihan untuk kelas baru.</p>
                                                        </div>
                                                    </label>

                                                    <label class="flex items-start text-xs text-gray-700 cursor-pointer select-none mt-2">
                                                        <input type="radio" wire:model.live="wizardBillingPolicy" value="delete_except_current_month" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        <div>
                                                            <span class="font-semibold text-primary">Hapus Kecuali Bulan Ini & Buat Baru</span>
                                                            <p class="text-[10px] text-gray-500 mt-0.5">Hapus tagihan kelas lama (unpaid) kecuali bulan ini, dan buat tagihan kelas baru.</p>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-2">
                                            @if($destinationLevelId !== 'lulus')
                                                <label class="block text-xs font-semibold text-gray-700 uppercase">Buat Tagihan Kelas Baru (Massal):</label>
                                                @if(empty($availableWizardBillings))
                                                    <p class="text-xs text-gray-500 bg-gray-50 p-2.5 rounded border border-dashed mt-1.5">Tidak ada tagihan khusus untuk kelas tujuan.</p>
                                                @else
                                                    <div class="space-y-2 mt-1.5 max-h-[120px] overflow-y-auto p-1.5 border rounded">
                                                        @foreach($availableWizardBillings as $billing)
                                                            <label class="flex items-center text-xs text-gray-700 cursor-pointer select-none">
                                                                <input type="checkbox" wire:model.live="wizardBillingCategories" value="{{ $billing['id'] }}" class="rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                                <div class="flex-1 flex justify-between pr-2">
                                                                    <span>{{ $billing['name'] }}</span>
                                                                    <span class="font-mono text-gray-500">Rp {{ number_format($billing['total_amount'], 0, ',', '.') }}</span>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-lg flex justify-between">
                        <button wire:click="wizardPrevStep" type="button" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 {{ $wizardStep === 1 ? 'invisible' : '' }}">
                            Kembali
                        </button>
                        
                        @if($wizardStep < 3)
                            <button wire:click="wizardNextStep" type="button" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-primary text-primary-foreground hover:bg-primary/90 font-medium transition">
                                Lanjut
                            </button>
                        @else
                            <button wire:click="executePromotion" wire:loading.attr="disabled" type="button" class="inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 disabled:opacity-50">
                                <svg wire:loading wire:target="executePromotion" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="executePromotion">
                                    {{ $destinationLevelId === 'lulus' ? 'Eksekusi Kelulusan Santri!' : 'Eksekusi Kenaikan Kelas!' }}
                                </span>
                                <span wire:loading wire:target="executePromotion">Memproses...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @script
    <script>
        $wire.on('swal:success', (event) => {
            const data = event[0] || event;
            window.Swal.fire({
                icon: 'success',
                title: data.title,
                text: data.text,
                confirmButtonColor: '#2e7d32'
            });
        });
    </script>
    @endscript
</div>
