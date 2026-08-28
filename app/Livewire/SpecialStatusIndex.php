<?php

namespace App\Livewire;

use App\Models\SpecialStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class SpecialStatusIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Modal Form Properties
    public $isOpen = false;
    public $isEdit = false;
    public $specialStatusId;
    public $code = '';
    public $name = '';
    public $description = '';
    public $is_system = false;
    public $is_visible = true;

    protected $rules = [
        'code' => 'required|string|max:50',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->dispatch('swal:show-form', [
            'isEdit' => false,
            'id' => null,
            'code' => '',
            'name' => '',
            'description' => '',
            'isSystem' => false,
            'isVisible' => true,
        ]);
    }

    public function edit($id)
    {
        $status = SpecialStatus::findOrFail($id);
        $this->dispatch('swal:show-form', [
            'isEdit' => true,
            'id' => $status->id,
            'code' => $status->code,
            'name' => $status->name,
            'description' => $status->description,
            'isSystem' => $status->is_system,
            'isVisible' => $status->is_visible,
        ]);
    }

    public function saveData($id, $code, $name, $description, $is_visible = true)
    {
        $originalCodeInput = $code;
        $status = $id ? SpecialStatus::find($id) : null;
        $isSystem = $status ? $status->is_system : false;

        if (!$isSystem) {
            $code = strtoupper(Str::slug($code, '_'));
        } else {
            $code = $status->code;
            $name = $status->name; // enforce original name for system
        }

        $validationRules = [
            'description' => 'nullable|string',
        ];

        if (!$isSystem) {
            $validationRules['code'] = 'required|string|max:50|unique:special_statuses,code,' . ($id ?: 'NULL');
            $validationRules['name'] = 'required|string|max:255';
        }

        $validator = \Illuminate\Support\Facades\Validator::make([
            'code' => $code,
            'name' => $name,
            'description' => $description,
        ], $validationRules, [
            'code.unique' => 'Kode Database "' . $code . '" sudah digunakan golongan lain.',
            'code.required' => 'Kode Database wajib diisi.',
            'name.required' => 'Nama Golongan wajib diisi.',
        ]);

        if ($validator->fails()) {
            $this->dispatch('swal:error', [
                'title' => 'Validasi Gagal',
                'text' => $validator->errors()->first(),
            ]);

            $this->dispatch('swal:show-form', [
                'isEdit' => !empty($id),
                'id' => $id,
                'code' => $originalCodeInput,
                'name' => $name,
                'description' => $description,
                'isSystem' => $isSystem,
                'isVisible' => $is_visible,
            ]);
            return;
        }

        if ($id) {
            $updateData = [
                'description' => $description,
                'is_visible' => $is_visible,
            ];

            if (!$isSystem) {
                $updateData['name'] = $name;
                $oldCode = $status->code;
                $newCode = $code;

                if ($oldCode !== $newCode) {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($status, $oldCode, $newCode, $updateData) {
                        // 1. Update parent record (triggers ON UPDATE CASCADE on pivot table student_special_statuses)
                        $updateData['code'] = $newCode;
                        $status->update($updateData);

                        // 3. Update Discount table manually
                        \App\Models\Discount::where('target_status', $oldCode)->update(['target_status' => $newCode]);
                    });
                } else {
                    $status->update($updateData);
                }
            } else {
                $status->update($updateData);
            }

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Golongan berhasil diperbarui.'
            ]);
        } else {
            SpecialStatus::create([
                'code' => $code,
                'name' => $name,
                'description' => $description,
                'is_system' => false,
                'is_visible' => $is_visible,
            ]);

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Golongan baru berhasil ditambahkan.'
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $status = SpecialStatus::findOrFail($id);

        if ($status->is_system) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Golongan sistem (seperti UMUM) tidak dapat dihapus.'
            ]);
            return;
        }

        // Fetch students using this status
        $students = \App\Models\Student::whereHas('specialStatuses', function ($q) use ($status) {
                $q->where('special_statuses.code', $status->code);
            })
            ->select('id', 'full_name', 'nis')
            ->get()
            ->map(fn($s) => "{$s->full_name} (" . ($s->nis ?: 'Belum ada NIS') . ")")
            ->toArray();

        // Check if used by discounts
        $discountCount = \App\Models\Discount::where('target_status', $status->code)->count();

        if (count($students) > 0 || $discountCount > 0) {
            $this->dispatch('swal:error-in-use', [
                'name' => $status->name,
                'students' => $students,
                'discountCount' => $discountCount,
            ]);
            return;
        }

        $this->dispatch('confirm-delete-special-status', [
            'id' => $id,
            'name' => $status->name,
        ]);
    }

    public function executeDelete($id)
    {
        $status = SpecialStatus::findOrFail($id);
        
        if ($status->is_system) return;

        // Final safety check
        $studentCount = \App\Models\Student::whereHas('specialStatuses', function ($q) use ($status) {
            $q->where('special_statuses.code', $status->code);
        })->count();
        $discountCount = \App\Models\Discount::where('target_status', $status->code)->count();
        if ($studentCount > 0 || $discountCount > 0) return;

        $status->delete();

        $this->dispatch('swal:success', [
            'title' => 'Terhapus!',
            'text' => 'Golongan berhasil dihapus.'
        ]);
    }

    public function render()
    {
        $statuses = SpecialStatus::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.special-status-index', [
            'statuses' => $statuses
        ])->layout('layouts.admin');
    }
}
